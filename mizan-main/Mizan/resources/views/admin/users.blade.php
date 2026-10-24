@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')
@section('page_title', 'إدارة المستخدمين')
@section('page_subtitle', 'إدارة حسابات الموكلين والمحامين')

@section('admin_users_active', 'nav-active')

@section('content')
<div class="space-y-5 relative">
    
    @if(session('success'))
        <div class="p-3.5 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-100 font-semibold flex items-center gap-2">
            <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-sm transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-medium mb-1">إجمالي المستخدمين</p>
                    <p class="text-slate-800 text-2xl font-bold">{{ $totalUsers }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-1.5">
                        <i class="fas fa-users-cog text-[10px]"></i> بدون المدراء
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                    <i class="fas fa-users text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-sm transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-medium mb-1">الموكلون</p>
                    <p class="text-slate-800 text-2xl font-bold">{{ $totalClients }}</p>
                    <p class="text-xs text-blue-600 font-medium mt-1.5">
                        <i class="fas fa-user-check text-[10px]"></i> العملاء
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                    <i class="fas fa-user text-lg"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-5 border border-slate-200 hover:shadow-sm transition-shadow">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-slate-400 text-xs font-medium mb-1">المحامون</p>
                    <p class="text-slate-800 text-2xl font-bold">{{ $totalLawyers }}</p>
                    <p class="text-xs text-emerald-600 font-medium mt-1.5">
                        <i class="fas fa-gavel text-[10px]"></i> مسجلون
                    </p>
                </div>
                <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-gavel text-lg"></i>
                </div>
            </div>
        </div>
    </div>


    <form action="{{ route('admin.users') }}" method="GET" class="bg-white rounded-xl p-4 flex flex-wrap justify-between items-center gap-3 border border-slate-200">
        <div class="flex-1 min-w-[250px] relative">
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو البريد..." 
                   class="w-full pr-10 pl-4 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors font-medium">
        </div>
        
        <div class="flex gap-2 w-full sm:w-auto">
            <select name="role" onchange="this.form.submit()" class="flex-1 sm:flex-none px-3 py-2.5 border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:border-blue-500 bg-white text-slate-700 cursor-pointer">
                <option value="جميع الأدوار" {{ request('role') == 'جميع الأدوار' ? 'selected' : '' }}>جميع الأدوار</option>
                <option value="client" {{ request('role') == 'client' ? 'selected' : '' }}>موكل</option>
                <option value="lawyer" {{ request('role') == 'lawyer' ? 'selected' : '' }}>محامي</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="flex-1 sm:flex-none px-3 py-2.5 border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:border-blue-500 bg-white text-slate-700 cursor-pointer">
                <option value="جميع الحالات" {{ request('status') == 'جميع الحالات' ? 'selected' : '' }}>جميع الحالات</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>نشط</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>محظور</option>
            </select>
            
            @if(request('search') || request('role') || request('status'))
                <a href="{{ route('admin.users') }}" class="px-3 py-2.5 bg-slate-100 text-slate-600 rounded-lg text-sm font-semibold hover:bg-slate-200 transition-colors flex items-center justify-center">
                    <i class="fas fa-undo text-xs"></i>
                </a>
            @endif
        </div>
    </form>


    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500">المستخدم</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500">البريد</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500">الدور</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500">التسجيل</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500">الحالة</th>
                        <th class="text-center px-5 py-3 text-xs font-semibold text-slate-500">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-slate-800 flex items-center justify-center text-white text-sm font-bold">
                                    {{ mb_substr($user->name, 0, 1, 'utf-8') }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-sm">{{ $user->name }}</p>
                                    <p class="text-[11px] text-slate-400">USR-{{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-600">{{ $user->email }}</td>
                        <td class="px-5 py-3.5">
                            @if($user->role === 'lawyer')
                                <span class="px-2.5 py-1 text-[11px] font-medium rounded-md bg-purple-50 text-purple-600">محامي</span>
                            @else
                                <span class="px-2.5 py-1 text-[11px] font-medium rounded-md bg-blue-50 text-blue-600">موكل</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-400">{{ $user->created_at ? $user->created_at->format('d/m/Y') : '—' }}</td>
                        <td class="px-5 py-3.5">
                            @if($user->status === 'approved')
                                <span class="px-2.5 py-1 text-[11px] font-medium rounded-md bg-emerald-50 text-emerald-600">نشط</span>
                            @elseif($user->status === 'pending')
                                <span class="px-2.5 py-1 text-[11px] font-medium rounded-md bg-amber-50 text-amber-600">معلق</span>
                            @else
                                <span class="px-2.5 py-1 text-[11px] font-medium rounded-md bg-rose-50 text-rose-600">محظور</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex justify-center items-center gap-2">
                                <button onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->status }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-[11px] font-bold transition-all duration-200 border border-blue-200 hover:border-blue-600">
                                    <i class="fas fa-pen text-[10px]"></i> تعديل
                                </button>
                                
                                <form action="{{ route('admin.users.toggle-block', $user->id) }}" method="POST" onsubmit="return confirm('تأكيد تغيير حالة هذا الحساب؟')">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->status === 'rejected')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-lg text-[11px] font-bold transition-all duration-200 border border-emerald-200 hover:border-emerald-600">
                                            <i class="fas fa-user-check text-[10px]"></i> تفعيل
                                        </button>
                                    @else
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg text-[11px] font-bold transition-all duration-200 border border-rose-200 hover:border-rose-600">
                                            <i class="fas fa-user-slash text-[10px]"></i> حظر
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">
                            <i class="fas fa-folder-open text-xl block mb-1.5 text-slate-300"></i>
                            لا يوجد مستخدمون
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        

        <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50">
            {{ $users->links() }}
        </div>
    </div>

    {{-- نافذة التعديل --}}
    <div id="editUserModal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-xl w-full max-w-md p-5 shadow-xl border border-slate-200 space-y-5 transform scale-95 transition-transform duration-300">
            
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                        <i class="fas fa-user-cog text-sm"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-slate-800">تعديل الحساب</h3>
                </div>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editUserForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">الاسم</label>
                    <input type="text" name="name" id="modalUserName" required class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:border-blue-500 text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">البريد الإلكتروني</label>
                    <input type="email" name="email" id="modalUserEmail" required class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:border-blue-500 text-slate-800">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">الدور</label>
                        <select name="role" id="modalUserRole" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:border-blue-500 bg-white text-slate-800">
                            <option value="client">موكل</option>
                            <option value="lawyer">محامي</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">الحالة</label>
                        <select name="status" id="modalUserStatus" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm font-medium focus:outline-none focus:border-blue-500 bg-white text-slate-800">
                            <option value="approved">نشط</option>
                            <option value="pending">معلق</option>
                            <option value="rejected">محظور</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold hover:bg-slate-200 transition-colors">إلغاء</button>
                    <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-xs font-semibold hover:bg-slate-700 transition-colors">حفظ</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function openEditModal(id, name, email, role, status) {
        const modal = document.getElementById('editUserModal');
        const form = document.getElementById('editUserForm');
        
        form.action = `/admin/users/${id}`;
        
        document.getElementById('modalUserName').value = name;
        document.getElementById('modalUserEmail').value = email;
        document.getElementById('modalUserRole').value = role;
        document.getElementById('modalUserStatus').value = status;

        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modal.querySelector('.transform').classList.remove('scale-95');
        }, 10);
    }

    function closeEditModal() {
        const modal = document.getElementById('editUserModal');
        modal.classList.add('opacity-0');
        modal.querySelector('.transform').classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); }, 300);
    }
</script>
@endsection