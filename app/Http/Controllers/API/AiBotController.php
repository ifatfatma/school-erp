<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AiBotFaq;
use App\Models\AiBotLog;
use App\Models\AiBotSetting;
use App\Models\School;
use App\Services\AiBotService;
use App\Traits\ResolvesSchoolId;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AiBotController extends Controller
{
    use ResolvesSchoolId;

    protected AiBotService $botService;

    public function __construct(AiBotService $botService)
    {
        $this->botService = $botService;
    }

    /**
     * Get Initial Bot Configuration & Welcome Payload (Public or Authenticated)
     */
    public function init(Request $request)
    {
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        if (!$schoolId && auth()->check()) {
            $schoolId = $this->resolveSchoolId($request);
        }

        $config = $this->botService->getInitialConfig($schoolId);
        return response()->json($config);
    }

    /**
     * Process User Chat Query
     */
    public function chat(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:1000',
            'school_id' => 'nullable|integer',
            'session_id' => 'nullable|string|max:100',
        ]);

        $query = $request->input('query');
        $schoolId = $request->input('school_id') ? (int) $request->input('school_id') : null;
        if (!$schoolId && auth()->check()) {
            $schoolId = $this->resolveSchoolId($request);
        }

        $sessionId = $request->input('session_id') ?: ($request->hasSession() ? $request->session()->getId() : Str::random(24));
        $userId = auth()->id();
        $ip = $request->ip();

        $result = $this->botService->answerQuery($query, $schoolId, $sessionId, $userId, $ip);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Log User Feedback on Bot Response
     */
    public function feedback(Request $request)
    {
        $request->validate([
            'log_id' => 'nullable|integer',
            'session_id' => 'nullable|string',
            'was_helpful' => 'required|boolean',
        ]);

        if ($request->filled('log_id')) {
            AiBotLog::where('id', $request->log_id)->update(['was_helpful' => $request->boolean('was_helpful')]);
        } elseif ($request->filled('session_id')) {
            AiBotLog::where('session_id', $request->session_id)
                ->latest()
                ->first()
                ?->update(['was_helpful' => $request->boolean('was_helpful')]);
        }

        return response()->json(['success' => true, 'message' => 'Feedback recorded']);
    }

    /**
     * ADMIN: Get Bot Settings
     */
    public function getSettings(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);
        $setting = AiBotSetting::firstOrCreate(
            ['school_id' => $schoolId],
            [
                'bot_name' => 'School AI Assistant',
                'welcome_message' => 'Hello! I am your 24/7 School AI Support Assistant. How can I help you today?',
                'whatsapp_number' => '+919331805678',
                'support_email' => 'support@school.com',
                'support_phone' => '+91 93318 05678',
                'office_hours' => 'Mon - Sat: 8:00 AM - 4:00 PM',
                'is_enabled' => true,
                'enable_whatsapp_button' => true,
                'enable_voice_speech' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'settings' => $setting,
        ]);
    }

    /**
     * ADMIN: Update Bot Settings
     */
    public function saveSettings(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'bot_name' => 'required|string|max:100',
            'welcome_message' => 'nullable|string|max:1000',
            'whatsapp_number' => 'required|string|max:50',
            'whatsapp_default_message' => 'nullable|string|max:500',
            'support_email' => 'nullable|email|max:150',
            'support_phone' => 'nullable|string|max:50',
            'office_hours' => 'nullable|string|max:150',
            'is_enabled' => 'nullable|boolean',
            'enable_whatsapp_button' => 'nullable|boolean',
            'enable_voice_speech' => 'nullable|boolean',
        ]);

        $setting = AiBotSetting::updateOrCreate(
            ['school_id' => $schoolId],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'AI Bot & WhatsApp settings saved successfully',
            'settings' => $setting,
        ]);
    }

    /**
     * ADMIN: List FAQs
     */
    public function listFaqs(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

        $faqs = AiBotFaq::where('school_id', $schoolId)
            ->orWhereNull('school_id')
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'faqs' => $faqs,
        ]);
    }

    /**
     * ADMIN: Store or Update FAQ
     */
    public function saveFaq(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'id' => 'nullable|integer',
            'category' => 'required|string|max:60',
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:5000',
            'keywords' => 'nullable|string|max:1000',
            'action_label' => 'nullable|string|max:100',
            'action_url' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $faqId = $request->input('id');
        if ($faqId) {
            $faq = AiBotFaq::where('school_id', $schoolId)->findOrFail($faqId);
            $faq->update($validated);
        } else {
            $validated['school_id'] = $schoolId;
            $faq = AiBotFaq::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Knowledge base FAQ saved successfully',
            'faq' => $faq,
        ]);
    }

    /**
     * ADMIN: Delete FAQ
     */
    public function deleteFaq(Request $request, $id)
    {
        $schoolId = $this->resolveSchoolId($request);
        $faq = AiBotFaq::where('school_id', $schoolId)->findOrFail($id);
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ entry deleted',
        ]);
    }

    /**
     * ADMIN: Get Analytics & Logs
     */
    public function logs(Request $request)
    {
        $schoolId = $this->resolveSchoolId($request);

        $logs = AiBotLog::where('school_id', $schoolId)
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get();

        $stats = [
            'total_queries' => AiBotLog::where('school_id', $schoolId)->count(),
            'whatsapp_escalations' => AiBotLog::where('school_id', $schoolId)->where('escalated_to_whatsapp', true)->count(),
            'helpful_count' => AiBotLog::where('school_id', $schoolId)->where('was_helpful', true)->count(),
            'unhelpful_count' => AiBotLog::where('school_id', $schoolId)->where('was_helpful', false)->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'logs' => $logs,
        ]);
    }
}
