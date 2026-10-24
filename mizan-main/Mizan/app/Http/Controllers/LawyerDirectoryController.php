<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Specialty; 
use Illuminate\Http\Request;

class LawyerDirectoryController extends Controller
{
    public function index(Request $request)
    {
        // 1. جلب جميع التخصصات
        $allSpecialties = Specialty::all();

        // 2. استعلام المحامين مع تحميل العلاقات (Eager Loading)
        // استخدام with هنا يغنينا عن الـ leftJoin ويجلب بيانات البروفايل والتخصص بشكل نظيف
        $query = User::where('users.role', 'lawyer')
            ->where('users.status', 'approved')
            ->withCount('cases') 
            ->with(['lawyerProfile.specialty']);

        // 3. البحث الذكي (بالاسم أو باسم التخصص)
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('lawyerProfile.specialty', function($subQ) use ($searchTerm) {
                      $subQ->where('name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // 4. الفلترة بالتخصص عبر الـ ID
        if ($request->filled('specialty_id') && $request->specialty_id !== 'all') {
            $query->whereHas('lawyerProfile', function($q) use ($request) {
                $q->where('specialty_id', $request->specialty_id);
            });
        }

        // 5. الترتيب
        if ($request->sort == 'experience') {
            $query->join('lawyer_profiles', 'users.id', '=', 'lawyer_profiles.user_id')
                  ->orderByRaw('COALESCE(lawyer_profiles.years_of_experience, 0) DESC')
                  ->select('users.*'); // اختيار أعمدة الـ users فقط لمنع تداخل الأعمدة
        } elseif ($request->sort == 'rating') {
            $query->join('lawyer_profiles', 'users.id', '=', 'lawyer_profiles.user_id')
                  ->orderByRaw('COALESCE(lawyer_profiles.rating, 0) DESC')
                  ->select('users.*');
        }

        $lawyers = $query->paginate(12);

        return view('client.lawyers.index', compact('lawyers', 'allSpecialties'));
    }

    public function show($id)
    {
        $lawyer = User::with(['lawyerProfile.specialty', 'cases', 'reviews'])
            ->where('role', 'lawyer')
            ->where('status', 'approved')
            ->findOrFail($id);

        return view('client.lawyers.profile', compact('lawyer'));
    }
}