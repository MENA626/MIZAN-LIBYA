<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\{
    AiController, AiAssistantController, AuthController, ClientChatController,
    ClientCaseController, ConsultationController, PaidConsultationController,
    PaymentController, LawyerChatController, LawyerProfileController,
    LawyerDirectoryController, AppointmentController, DocumentController,
    LawyerCaseController, NotificationController
};
use App\Http\Controllers\Lawyer\LawyerDashboardController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Admin\{
    DashboardController, AdminLawyerVerificationController, UserController, AdminSettingsController, AnalyticsController
};

// 1. الصفحة الرئيسية
Route::get('/', fn() => view('index'))->name('home');

// 2. المصادقة (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register.form');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/verify-otp', [PasswordResetController::class, 'showVerifyOtpForm'])->name('verify.otp');
    Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('verify.otp.post');
    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// 3. المسارات المحمية والمشتركة
Route::middleware(['auth'])->group(function () {
    
    // خدمات عامة ومشتركة
    Route::post('/consultations/store', [PaidConsultationController::class, 'store'])->name('consultations.store');
    Route::get('/payment/checkout/{id}', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // المكتبة الذكية (الوصول المشترك)
    Route::get('/client/smart_library', [DocumentController::class, 'index'])->name('client.smart_library.index');
    Route::get('/lawyer/filemanager', [DocumentController::class, 'index'])->name('lawyer.filemanager');

    Route::prefix('mizan')->group(function () {
        Route::post('/folders', [DocumentController::class, 'storeFolder'])->name('folders.store');
        Route::put('/folders/{id}', [DocumentController::class, 'updateFolder'])->name('folders.update');
        Route::delete('/folders/{id}', [DocumentController::class, 'destroyFolder'])->name('folders.destroy');
        Route::post('/documents', [DocumentController::class, 'storeDocument'])->name('documents.store');
        Route::delete('/documents/{id}', [DocumentController::class, 'destroyDocument'])->name('documents.destroy');
        Route::get('/documents/{id}/view', [DocumentController::class, 'viewDocument'])->name('documents.view');
        Route::put('/documents/{id}/name', [DocumentController::class, 'updateDocumentName'])->name('documents.updateName');
        Route::put('/documents/{id}/metadata', [DocumentController::class, 'updateDocumentMetadata'])->name('documents.updateMetadata');
        Route::post('/documents/summarize', [DocumentController::class, 'summarizeDocument'])->name('documents.summarize');
        Route::post('/summaries/store', [DocumentController::class, 'storeSummary'])->name('summaries.store');
        Route::get('/summaries', [DocumentController::class, 'getSummaries'])->name('summaries.index');
        Route::get('/summaries/{id}', [DocumentController::class, 'getSummary'])->name('summaries.show');
        Route::delete('/summaries/{id}', [DocumentController::class, 'destroySummary'])->name('summaries.destroy');
    });

    // الذكاء الاصطناعي (مشترك)
    Route::get('/ai-briefs', [AiAssistantController::class, 'getConversations'])->name('ai.briefs.get');
    Route::get('/ai-briefs/{id}/messages', [AiAssistantController::class, 'getMessages'])->name('ai.briefs.messages');
    Route::post('/ai-briefs', [AiAssistantController::class, 'storeConversation'])->name('ai.briefs.store');
    Route::delete('/ai-briefs/{id}', [AiAssistantController::class, 'deleteConversation'])->name('ai.briefs.destroy');
    Route::post('/ask-ai', [AiController::class, 'testAi'])->name('ai.ask');

    // 4. مسارات العميل (Client)
    Route::middleware(['role:client'])->prefix('client')->name('client.')->group(function () {
        Route::post('/appointments/book', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::get('/dashboard', fn() => view('client.dashboard'))->name('dashboard');
        Route::get('/consultations', [PaidConsultationController::class, 'index'])->name('consultations');
        Route::get('/settings', fn() => view('client.settings'))->name('settings');
        Route::get('/lawyers', [LawyerDirectoryController::class, 'index'])->name('lawyers.index');
        Route::get('/lawyers/{id}', [LawyerDirectoryController::class, 'show'])->name('lawyers.show');
        Route::get('/my-case', [ClientCaseController::class, 'index'])->name('my-case');
        Route::get('/my-case/{id}', [ClientCaseController::class, 'show'])->name('my-case.show');
        Route::post('/my-case/tasks/{id}/toggle', [ClientCaseController::class, 'toggleTask'])->name('my-case.tasks.toggle');
        Route::get('/my-case/documents/{id}/download', [ClientCaseController::class, 'downloadDocument'])->name('my-case.documents.download');
        Route::post('/my-case/{id}/checkout', [PaymentController::class, 'checkoutCase'])->name('my-case.checkout');
        Route::get('/my-case-payment/callback', [PaymentController::class, 'callbackCase'])->name('my-case.payment.callback');
        Route::post('/settings/password', [\App\Http\Controllers\ClientSettingsController::class, 'updatePassword'])->name('settings.password');
        Route::put('/settings/profile', [\App\Http\Controllers\ClientSettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/notifications', [\App\Http\Controllers\ClientSettingsController::class, 'updateNotifications'])->name('settings.notifications');
        Route::delete('/settings/account', [\App\Http\Controllers\ClientSettingsController::class, 'deleteAccount'])->name('settings.deleteAccount');
        Route::get('/assistant', [AiAssistantController::class, 'index'])->name('assistant');
        Route::get('/chat', fn() => redirect()->route('client.consultations'))->name('chat');
    });

    // 5. مسارات المحامي (Lawyer)
    Route::middleware(['role:lawyer', 'verified.lawyer'])->prefix('lawyer')->name('lawyer.')->group(function () {
        Route::get('/dashboard', [LawyerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/settings', fn() => view('lawyer.settings'))->name('settings');
        Route::get('/chat', [LawyerChatController::class, 'index'])->name('chat');
        Route::get('/my-cases', [LawyerCaseController::class, 'index'])->name('my-cases');
        Route::get('/cases/{id}', [LawyerCaseController::class, 'show'])->name('cases.show');
        Route::post('/cases/search-client', [LawyerCaseController::class, 'searchClient'])->name('cases.search-client');
        Route::post('/cases', [LawyerCaseController::class, 'store'])->name('cases.store');
        Route::put('/cases/{id}', [LawyerCaseController::class, 'update'])->name('cases.update');
        Route::delete('/cases/{id}', [LawyerCaseController::class, 'destroy'])->name('cases.destroy');
        Route::post('/cases/{id}/sessions', [LawyerCaseController::class, 'storeSession'])->name('cases.sessions.store');
        Route::put('/cases/{id}/sessions/{sessionId}', [LawyerCaseController::class, 'updateSession'])->name('cases.sessions.update');
        Route::delete('/cases/{id}/sessions/{sessionId}', [LawyerCaseController::class, 'destroySession'])->name('cases.sessions.destroy');
        Route::post('/cases/{id}/updates', [LawyerCaseController::class, 'storeUpdate'])->name('cases.updates.store');
        Route::delete('/cases/{id}/updates/{updateId}', [LawyerCaseController::class, 'destroyUpdate'])->name('cases.updates.destroy');
        Route::post('/cases/{id}/tasks', [LawyerCaseController::class, 'storeTask'])->name('cases.tasks.store');
        Route::delete('/cases/{id}/tasks/{taskId}', [LawyerCaseController::class, 'destroyTask'])->name('cases.tasks.destroy');
        Route::post('/cases/{id}/documents', [LawyerCaseController::class, 'storeDocument'])->name('cases.documents.store');
        Route::delete('/cases/{id}/documents/{docId}', [LawyerCaseController::class, 'destroyDocument'])->name('cases.documents.destroy');
        Route::get('/consultations', [PaidConsultationController::class, 'lawyerIndex'])->name('consultations');
        Route::post('/consultations/{id}/review', [PaidConsultationController::class, 'lawyerReview'])->name('consultations.review');
        Route::get('/appointments', [AppointmentController::class, 'lawyerAppointments'])->name('appointments');
        Route::post('/appointments/{id}/approve', [AppointmentController::class, 'approve'])->name('appointments.approve');
        Route::post('/appointments/{id}/reject', [AppointmentController::class, 'reject'])->name('appointments.reject');
        Route::post('/appointments/bulk-delete', [AppointmentController::class, 'bulkDelete'])->name('appointments.bulkDelete');
        Route::get('/wallet', [\App\Http\Controllers\LawyerWalletController::class, 'index'])->name('wallet');
        Route::post('/wallet/withdraw', [\App\Http\Controllers\LawyerWalletController::class, 'withdraw'])->name('wallet.withdraw');
        Route::get('/ai-consultant', [AiAssistantController::class, 'index'])->name('ai_consultant');
        Route::put('/settings/profile', [LawyerProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/settings/professional', [LawyerProfileController::class, 'updateProfessionalInfo'])->name('settings.updateProfessionalInfo');
        Route::put('/settings/financial', [LawyerProfileController::class, 'updateFinancial'])->name('settings.financial');
        Route::put('/settings/update-financial', [LawyerProfileController::class, 'updateFinancial'])->name('settings.updateFinancial');
        Route::put('/settings/working-hours', [LawyerProfileController::class, 'updateWorkingHours'])->name('settings.updateWorkingHours');
        Route::put('/settings/notifications', [LawyerProfileController::class, 'updateNotifications'])->name('settings.updateNotifications');
        Route::put('/settings/password', [LawyerProfileController::class, 'updatePassword'])->name('settings.updatePassword');
        Route::delete('/settings/delete-account', [LawyerProfileController::class, 'deleteAccount'])->name('settings.deleteAccount');
    });

    // 6. مسارات الشات API
    Route::prefix('api/chat')->group(function () {
        Route::post('/start', [ClientChatController::class, 'startConversation']);
        Route::post('/send-message', [ClientChatController::class, 'sendMessage']);
        Route::get('/messages/{sessionId}', [ClientChatController::class, 'getMessages']);
        Route::get('/check-status/{sessionId}', [ClientChatController::class, 'checkStatus']);
        Route::post('/submit-satisfaction', [ClientChatController::class, 'submitSatisfaction']);
        Route::post('/close-without-feedback', [ClientChatController::class, 'closeWithoutFeedback']);
        Route::post('/report-dispute', [ClientChatController::class, 'reportDispute']);
        Route::post('/rate', [ClientChatController::class, 'rateLawyer']);
        Route::get('/conversations', [ClientChatController::class, 'getConversations']);
        Route::get('/lawyers', [ClientChatController::class, 'getAvailableLawyers']);
        Route::delete('/delete/{id}', [ClientChatController::class, 'deleteConversation'])->name('chat.delete');
        Route::get('/preview/{filePath}', [ClientChatController::class, 'previewAttachment'])->where('filePath', '.*')->name('chat.preview');
        Route::get('/download/{filePath}', [ClientChatController::class, 'downloadAttachment'])->where('filePath', '.*')->name('chat.download');
        Route::delete('/attachment/{messageId}', [ClientChatController::class, 'deleteAttachment'])->name('chat.attachment.delete');
        Route::get('/lawyer/conversations', [LawyerChatController::class, 'getConversations']);
        Route::get('/lawyer/messages/{id}', [LawyerChatController::class, 'getMessages']);
        Route::post('/lawyer/send', [LawyerChatController::class, 'sendMessage']);
        Route::post('/lawyer/end-consultation', [LawyerChatController::class, 'endConsultation']);
        Route::get('/lawyer/preview/{filePath}', [LawyerChatController::class, 'previewAttachment'])->where('filePath', '.*')->name('lawyer.chat.preview');
        Route::get('/lawyer/download/{filePath}', [LawyerChatController::class, 'downloadAttachment'])->where('filePath', '.*')->name('lawyer.chat.download');
    });

    // 7. مسارات المدير (Admin)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // --- إضافة مسار عرض المحادثة للأدمن ---
        Route::get('/disputes/chat/{id}', [\App\Http\Controllers\AdminFinanceController::class, 'viewChat'])
             ->name('disputes.view-chat');

        // --- المسار الجديد الذي تمت إضافته هنا ---
        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
        
        Route::get('/users', [UserController::class, 'index'])->name('users');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::get('/finance', [\App\Http\Controllers\AdminFinanceController::class, 'index'])->name('finance');
        Route::post('/finance/withdrawals/{id}/approve', [\App\Http\Controllers\AdminFinanceController::class, 'approveWithdrawal'])->name('finance.withdrawals.approve');
        Route::post('/finance/disputes/{id}/resolve', [\App\Http\Controllers\AdminFinanceController::class, 'resolveDispute'])->name('finance.disputes.resolve');
        Route::get('/ai-assistant', [AiAssistantController::class, 'index'])->name('ai_assistant');
        Route::patch('/users/{id}/toggle-block', [UserController::class, 'toggleBlock'])->name('users.toggle-block');
        Route::get('/verification', [AdminLawyerVerificationController::class, 'index'])->name('verification');
        Route::post('/lawyers/{id}/approve', [AdminLawyerVerificationController::class, 'approve'])->name('lawyers.approve');
        Route::post('/lawyers/{id}/reject', [AdminLawyerVerificationController::class, 'reject'])->name('lawyers.reject');
        Route::get('/all-cases', function() {
            return view('admin.all-cases', [
                'cases' => \App\Models\LegalCase::with('lawyer')->whereNull('archived_at')->orderBy('created_at', 'desc')->paginate(15),
                'totalCases' => \App\Models\LegalCase::whereNull('archived_at')->count(),
                'pendingCases' => \App\Models\LegalCase::whereNull('archived_at')->where('status', 'pending')->count(),
                'completedCases' => \App\Models\LegalCase::whereNull('archived_at')->where('status', 'closed')->count()
            ]);
        })->name('all-cases');
        
        Route::get('/cases/{id}', function($id) {
            $case = \App\Models\LegalCase::with(['lawyer', 'client'])->findOrFail($id);
            return view('admin.show-case', compact('case')); 
        })->name('cases.show');

        // --- مسار أرشفة القضية ---
        Route::patch('/cases/{id}/archive', function($id) {
            $case = \App\Models\LegalCase::findOrFail($id);
            $case->update(['archived_at' => now()]);
            return back()->with('success', 'تمت أرشفة القضية بنجاح');
        })->name('cases.archive');
        
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
        Route::put('/settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('settings.profile');
        Route::put('/settings/password', [AdminSettingsController::class, 'updatePassword'])->name('settings.password');
        Route::put('/settings/notifications', [AdminSettingsController::class, 'updateNotifications'])->name('settings.notifications');
        Route::delete('/settings/delete-account', [AdminSettingsController::class, 'deleteAccount'])->name('settings.deleteAccount');
        Route::get('/run-embeddings', function () {
            Artisan::call('laws:generate-embeddings');
            return 'تمت أرشفة القوانين بنجاح!';
        });
    });
});

// 8. التنظيف الدوري
Cache::remember('notifications_weekly_cleanup', 604800, function () {
    DB::table('notifications')->where('created_at', '<', now()->subDays(7))->delete();
    return true;
});