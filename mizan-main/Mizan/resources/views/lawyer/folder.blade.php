@extends('layouts.lawyer')

@section('title', 'محتويات المجلد')
@section('page_title', 'قضية رقم #102')
@section('page_subtitle', 'عرض جميع ملفات ومستندات القضية')

@section('lawyer_filemanager_active', 'nav-active')

@section('content')
<div class="space-y-6 max-w-[1400px] mx-auto">
    
    {{-- شريط التنقل (Breadcrumb) --}}
    <div class="flex items-center gap-2 text-xs text-slate-400">
        <a href="{{ url('/lawyer/filemanager') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1">
            <i class="fas fa-folder-open"></i> خزانة الملفات
        </a>
        <i class="fas fa-chevron-left text-[9px]"></i>
        <span class="text-blue-600 font-semibold">قضية رقم #102</span>
    </div>
    
    {{-- معلومات المجلد --}}
    <div class="bg-slate-900 rounded-xl p-6 text-white shadow-sm">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-lg bg-white/10 flex items-center justify-center">
                    <i class="fas fa-folder-open text-3xl text-blue-400"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-white">قضية رقم #102</h1>
                    <p class="text-slate-400 text-xs mt-1">12 ملف • آخر تحديث: اليوم</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button id="uploadToFolderBtn" class="btn btn-primary btn-sm">
                    <i class="fas fa-upload text-xs"></i> رفع ملف هنا
                </button>
                <button onclick="history.back()" class="btn btn-secondary btn-sm bg-white/10 text-white border-white/20 hover:bg-white/20">
                    <i class="fas fa-arrow-right text-xs"></i> رجوع
                </button>
            </div>
        </div>
    </div>
    
    {{-- إحصائيات سريعة --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 text-center border border-slate-200 shadow-sm">
            <i class="fas fa-file-pdf text-red-500 text-2xl mb-2 block"></i>
            <p class="text-2xl font-bold text-slate-800">5</p>
            <p class="text-xs text-slate-400 mt-1">ملفات PDF</p>
        </div>
        <div class="bg-white rounded-xl p-4 text-center border border-slate-200 shadow-sm">
            <i class="fas fa-file-word text-blue-500 text-2xl mb-2 block"></i>
            <p class="text-2xl font-bold text-slate-800">3</p>
            <p class="text-xs text-slate-400 mt-1">مستندات Word</p>
        </div>
        <div class="bg-white rounded-xl p-4 text-center border border-slate-200 shadow-sm">
            <i class="fas fa-file-image text-purple-500 text-2xl mb-2 block"></i>
            <p class="text-2xl font-bold text-slate-800">2</p>
            <p class="text-xs text-slate-400 mt-1">صور ومرفقات</p>
        </div>
        <div class="bg-white rounded-xl p-4 text-center border border-slate-200 shadow-sm">
            <i class="fas fa-chart-line text-emerald-500 text-2xl mb-2 block"></i>
            <p class="text-2xl font-bold text-slate-800">12.5</p>
            <p class="text-xs text-slate-400 mt-1">MB المستخدمة</p>
        </div>
    </div>
    
    {{-- قائمة الملفات --}}
    <div class="card overflow-hidden">
        <div class="card-header bg-slate-50/50 flex justify-between items-center flex-wrap gap-3">
            <h3 class="text-slate-800 text-sm font-semibold flex items-center gap-2">
                <i class="fas fa-file-alt text-blue-600"></i>
                جميع الملفات
            </h3>
            <div class="relative">
                <i class="fas fa-search absolute right-3 top-1/2 transform -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" placeholder="بحث في الملفات..." 
                       class="form-input pr-9 py-1.5 text-xs w-60">
            </div>
        </div>
        
        <div class="table-wrapper">
            <table class="table-base">
                <thead>
                    <tr>
                        <th class="text-right">اسم الملف</th>
                        <th class="text-right">النوع</th>
                        <th class="text-right">تاريخ الرفع</th>
                        <th class="text-right">الحجم</th>
                        <th class="text-right">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-slate-800">
                            <i class="fas fa-file-pdf text-red-500 ml-2"></i>
                            عقد البيع النهائي.pdf
                        </td>
                        <td>
                            <span class="badge badge-red">PDF</span>
                        </td>
                        <td class="text-slate-500">15 أبريل 2025</td>
                        <td class="text-slate-500">2.4 MB</td>
                        <td>
                            <div class="flex gap-2">
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-blue-200 hover:border-blue-600"><i class="fas fa-download text-[9px]"></i> تحميل</button>
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 text-slate-500 hover:bg-slate-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-slate-200 hover:border-slate-600"><i class="fas fa-share-alt text-[9px]"></i> مشاركة</button>
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-rose-200 hover:border-rose-600"><i class="fas fa-trash-alt text-[9px]"></i> حذف</button>
                            </div>
                         </td>
                     </tr>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-slate-800">
                            <i class="fas fa-file-word text-blue-500 ml-2"></i>
                            مرافعة قضية رقم 102.docx
                         </td>
                        <td>
                            <span class="badge badge-blue">DOCX</span>
                         </td>
                        <td class="text-slate-500">14 أبريل 2025</td>
                        <td class="text-slate-500">1.2 MB</td>
                        <td>
                            <div class="flex gap-2">
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-blue-200 hover:border-blue-600"><i class="fas fa-download text-[9px]"></i> تحميل</button>
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 text-slate-500 hover:bg-slate-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-slate-200 hover:border-slate-600"><i class="fas fa-share-alt text-[9px]"></i> مشاركة</button>
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-rose-200 hover:border-rose-600"><i class="fas fa-trash-alt text-[9px]"></i> حذف</button>
                            </div>
                         </td>
                     </tr>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-slate-800">
                            <i class="fas fa-file-image text-purple-500 ml-2"></i>
                            إيصالات الدفع.jpg
                         </td>
                        <td>
                            <span class="badge badge-gray bg-purple-50 text-purple-600 border border-purple-100">IMAGE</span>
                         </td>
                        <td class="text-slate-500">13 أبريل 2025</td>
                        <td class="text-slate-500">0.8 MB</td>
                        <td>
                            <div class="flex gap-2">
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-blue-200 hover:border-blue-600"><i class="fas fa-download text-[9px]"></i> تحميل</button>
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 text-slate-500 hover:bg-slate-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-slate-200 hover:border-slate-600"><i class="fas fa-share-alt text-[9px]"></i> مشاركة</button>
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-rose-200 hover:border-rose-600"><i class="fas fa-trash-alt text-[9px]"></i> حذف</button>
                            </div>
                         </td>
                     </tr>
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-3 font-semibold text-slate-800">
                            <i class="fas fa-file-pdf text-red-500 ml-2"></i>
                            مستندات القضية.pdf
                         </td>
                        <td>
                            <span class="badge badge-red">PDF</span>
                         </td>
                        <td class="text-slate-500">10 أبريل 2025</td>
                        <td class="text-slate-500">3.1 MB</td>
                        <td>
                            <div class="flex gap-2">
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-blue-200 hover:border-blue-600"><i class="fas fa-download text-[9px]"></i> تحميل</button>
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-50 text-slate-500 hover:bg-slate-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-slate-200 hover:border-slate-600"><i class="fas fa-share-alt text-[9px]"></i> مشاركة</button>
                                <button class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white rounded-lg text-[10px] font-bold transition-all duration-200 border border-rose-200 hover:border-rose-600"><i class="fas fa-trash-alt text-[9px]"></i> حذف</button>
                            </div>
                         </td>
                     </tr>
                </tbody>
             </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-slate-100 flex justify-between items-center">
            <p class="text-xs text-slate-400">عرض 1-4 من 12</p>
            <div class="flex gap-1.5">
                <button class="btn btn-secondary py-1.5 px-3 text-xs disabled:opacity-50" disabled>السابق</button>
                <button class="btn btn-primary py-1.5 px-3 text-xs">1</button>
                <button class="btn btn-secondary py-1.5 px-3 text-xs">2</button>
                <button class="btn btn-secondary py-1.5 px-3 text-xs">3</button>
                <button class="btn btn-secondary py-1.5 px-3 text-xs">التالي</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal رفع ملف إلى المجلد --}}
<div id="uploadToFolderModal" class="modal-overlay hidden">
    <div class="modal-content max-w-md p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-cloud-upload-alt text-blue-600"></i>
                رفع ملف إلى المجلد
            </h3>
            <button onclick="closeUploadToFolderModal()" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
        
        <form id="uploadToFolderForm" class="space-y-4">
            <div>
                <label class="form-label">اختر ملف</label>
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center cursor-pointer hover:border-blue-500 transition-colors" id="folderDropzone">
                    <i class="fas fa-cloud-upload-alt text-3xl text-slate-300 mb-2"></i>
                    <p class="text-slate-500 text-sm">اسحب ملفك هنا أو انقر للاختيار</p>
                    <input type="file" id="folderFileInput" class="hidden">
                </div>
            </div>
            
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeUploadToFolderModal()" class="btn btn-secondary flex-1">إلغاء</button>
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="fas fa-upload text-xs"></i> رفع الملف
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const uploadToFolderModal = document.getElementById('uploadToFolderModal');
    const uploadToFolderBtn = document.getElementById('uploadToFolderBtn');
    const folderDropzone = document.getElementById('folderDropzone');
    const folderFileInput = document.getElementById('folderFileInput');
    
    function openUploadToFolderModal() {
        uploadToFolderModal.classList.remove('hidden');
    }
    
    function closeUploadToFolderModal() {
        uploadToFolderModal.classList.add('hidden');
    }
    
    uploadToFolderBtn.addEventListener('click', openUploadToFolderModal);
    
    folderDropzone.addEventListener('click', () => folderFileInput.click());
    folderDropzone.addEventListener('dragover', (e) => { e.preventDefault(); folderDropzone.classList.add('border-blue-500'); });
    folderDropzone.addEventListener('dragleave', (e) => { e.preventDefault(); folderDropzone.classList.remove('border-blue-500'); });
    folderDropzone.addEventListener('drop', (e) => { e.preventDefault(); folderDropzone.classList.remove('border-blue-500'); });
    
    document.getElementById('uploadToFolderForm').addEventListener('submit', (e) => {
        e.preventDefault();
        alert('✅ تم رفع الملف إلى المجلد بنجاح!');
        closeUploadToFolderModal();
    });
    
    uploadToFolderModal.addEventListener('click', (e) => { if (e.target === uploadToFolderModal) closeUploadToFolderModal(); });
</script>
@endsection