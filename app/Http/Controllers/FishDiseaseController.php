<?php

namespace App\Http\Controllers;

use App\Models\AiAnalysisLog;
use App\Models\Tank;
use App\Models\TankReading;
use App\Models\Threshold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class FishDiseaseController extends Controller
{
    private const DAILY_LIMIT = 50;

    public function index(Request $request)
    {
        $usageCount = $this->todayUsageCount($request->user()->id);

        return view('image-analysis.index', [
            'usageCount' => $usageCount,
            'dailyLimit' => self::DAILY_LIMIT,
            'remainingCount' => max(0, self::DAILY_LIMIT - $usageCount),
        ]);
    }

    public function checkDisease(Request $request)
    {
        $user = $request->user();
        $usageCount = $this->todayUsageCount($user->id);

        if ($usageCount >= self::DAILY_LIMIT) {
            return response()->json([
                'success' => false,
                'error' => 'Daily AI analysis limit reached. Please try again tomorrow.',
                'usage' => $usageCount,
                'remaining' => 0,
                'limit' => self::DAILY_LIMIT,
            ], 429);
        }

        $validated = $request->validate([
            'aquarium_photo' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'],
        ]);

        $imageFile = $validated['aquarium_photo'];
        $imageMime = $imageFile->getMimeType();
        $image64 = base64_encode(file_get_contents($imageFile->getRealPath()));

        $geminiKey = config('services.gemini.key');

        $geminiUrl = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key=" . $geminiKey;

        $systemPrompt = "
You are an AI fish health assistant.

Analyze the uploaded fish image and provide a preliminary visual health assessment.

IMPORTANT RULES:
- Respond ONLY in English.
- Do NOT use Malay.
- Do NOT use Markdown.
- Do NOT use code blocks.
- Do NOT output anything outside the JSON.
- Base your assessment only on visible symptoms in the image.
- Do NOT claim a definitive medical or veterinary diagnosis.
- This is only a preliminary visual assessment.
- If no visible symptoms are detected, use status as 'HEALTHY'.
- If visible symptoms are detected, use status as 'POSSIBLE DISEASE DETECTED'.
- If the fish appears healthy, use disease_name as 'No Visible Disease Detected'.
- visible_symptoms must be an array.
- recommendations must be an array.
- confidence_level must be your estimated confidence percentage, such as '85%', '92%', or '98%'.

Return ONLY this JSON format:

{
  \"status\": \"HEALTHY or POSSIBLE DISEASE DETECTED\",
  \"disease_name\": \"Disease name or No Visible Disease Detected\",
  \"visible_symptoms\": [
    \"symptom 1\",
    \"symptom 2\"
  ],
  \"recommendations\": [
    \"recommendation 1\",
    \"recommendation 2\"
  ],
  \"confidence_level\": \"estimated percentage\"
}
";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        [
                            'text' => $systemPrompt
                        ],
                        [
                            'inlineData' => [
                                'mimeType' => $imageMime,
                                'data' => $image64
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($geminiUrl, $payload);

        if ($response->failed()) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to connect to Gemini API.',
                'raw_google_error' => $response->json()
            ], 500);
        }

        $resultData = $response->json();

        $aiTextResponse = $resultData['candidates'][0]['content']['parts'][0]['text'] ?? '';

        $cleanJson = trim(str_replace(['```json', '```'], '', $aiTextResponse));

        $finalOutput = json_decode($cleanJson, true);

        if (!$finalOutput || !is_array($finalOutput)) {
            return response()->json([
                'success' => false,
                'error' => 'AI returned invalid response.',
                'raw_response' => $aiTextResponse
            ], 500);
        }

        $status = $finalOutput['status'] ?? 'UNKNOWN';
        $diseaseName = $finalOutput['disease_name'] ?? 'Unknown';
        $confidenceLevel = $finalOutput['confidence_level'] ?? '0%';
        $storedImageUrl = $this->storeAnalysisImage($imageFile);
        $selectedTank = Tank::where('user_id', $user->id)
            ->where('id', $request->session()->get('selected_tank_id'))
            ->first();

        AiAnalysisLog::create([
            'user_id' => $user->id,
            'tank_id' => $selectedTank?->id,
            'image_name' => $imageFile->getClientOriginalName(),
            'status' => $status,
            'disease_name' => $diseaseName,
            'confidence_level' => $confidenceLevel,
        ]);

        $usageCount++;

        return response()->json([
            'success' => true,
            'status' => $status,
            'disease_name' => $diseaseName,
            'visible_symptoms' => $finalOutput['visible_symptoms'] ?? [],
            'recommendations' => $finalOutput['recommendations'] ?? [],
            'confidence_level' => $confidenceLevel,
            'image_url' => $storedImageUrl,
            'usage' => $usageCount,
            'remaining' => max(0, self::DAILY_LIMIT - $usageCount),
            'limit' => self::DAILY_LIMIT,
        ], 200);
    }

    public function treatmentGuide(Request $request)
    {
        $selectedTank = Tank::where('user_id', $request->user()->id)
            ->where('id', $request->session()->get('selected_tank_id'))
            ->first();

        return view('image-analysis.treatment-guide', [
            'sensorReadings' => $selectedTank ? $this->sensorReadings($selectedTank) : collect(),
        ]);
    }

    private function todayUsageCount(int $userId): int
    {
        return AiAnalysisLog::where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])
            ->count();
    }

    private function storeAnalysisImage($imageFile): ?string
    {
        try {
            $directory = public_path('images/ai-analysis');

            if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
                return null;
            }

            $extension = $imageFile->getClientOriginalExtension() ?: 'jpg';
            $filename = Str::uuid() . '.' . $extension;
            $imageFile->move($directory, $filename);

            return asset('images/ai-analysis/' . $filename);
        } catch (Throwable) {
            return null;
        }
    }

    private function sensorReadings(Tank $tank)
    {
        $defaults = collect([
            'pH' => ['label' => 'pH', 'unit' => 'pH', 'min' => 6.5, 'max' => 7.5],
            'Turbidity' => ['label' => 'Turbidity', 'unit' => 'NTU', 'min' => 0, 'max' => 5],
            'Water Level' => ['label' => 'Water Level', 'unit' => 'cm', 'min' => 20, 'max' => 30],
        ]);

        $thresholds = Threshold::whereIn('parameter', $defaults->keys()->all())
            ->get()
            ->keyBy('parameter');

        return $defaults->map(function (array $definition, string $parameter) use ($tank, $thresholds) {
            $reading = TankReading::where('tank_id', $tank->id)
                ->where('parameter', $parameter)
                ->orderByDesc('recorded_at')
                ->orderByDesc('id')
                ->first();

            $threshold = $thresholds->get($parameter);
            $min = $threshold && is_numeric($threshold->min_value) ? (float) $threshold->min_value : $definition['min'];
            $max = $threshold && is_numeric($threshold->max_value) ? (float) $threshold->max_value : $definition['max'];
            $value = $reading && is_numeric($reading->value) ? (float) $reading->value : null;

            return [
                'label' => $definition['label'],
                'value' => $value,
                'unit' => $reading?->unit ?: $definition['unit'],
                'min' => $min,
                'max' => $max,
                'status' => $this->sensorStatus($value, $min, $max),
                'recorded_at' => $reading?->recorded_at,
            ];
        });
    }

    private function sensorStatus(?float $value, float $min, float $max): string
    {
        if ($value === null) {
            return 'Not available';
        }

        if ($value < $min) {
            return 'Low';
        }

        if ($value > $max) {
            return 'High';
        }

        return 'Normal';
    }
}
