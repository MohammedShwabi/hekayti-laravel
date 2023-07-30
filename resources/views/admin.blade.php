@extends('layouts.app')

@section('content')
<div class="container-fluid full-page">

    <!-- start of loading overlay element -->
    <div id="loading-overlay">
        <div class="spinner"></div>
    </div>
    <!-- end of loading overlay element -->

    <!-- start of page title section -->
    <div class="row text-center mt-4">
        <div class="page-title">
            إدارة المدراء
        </div>
    </div>
    <!-- end of page title section -->


    <!-- start of admin search section -->
    <div class="row mt-4 d-flex justify-content-center align-items-center">
        <div class="col-md-9">
            <form method="get" action="{{ route('manage') }}">
                <div class="search form-group">
                    <span class="search-icon icon-bordered">
                        <i class="fa-solid fa-magnifying-glass fa-flip-horizontal" id="search_icon" style="--fa-animation-duration: 1s;"></i>
                    </span>
                    <input type="text" value="{{$search}}" name="search" id="search_txt" oninput="searchInput('manage', { search: this.value.trim() } )" autocomplete="off" class="form-control shadow-none" placeholder="بحث عن مدير ...">
                    <input type="submit" id="search_btn" value="بحث" class="btn btn-primary">
                </div>
            </form>
        </div>
        <!-- search result list  -->
        <div class="col-md-9">
            <div class="list-group" id="result_list"></div>
        </div>
    </div>
    <!-- end of admin search section -->


    <!-- start of float add button -->
    <a class="btn overflow-visible add-btn text-white shadow" href="#" role="button" data-bs-toggle="modal" data-bs-target="#add_manager">
        <i class="fa fa-add"></i>
        <span style="display: none;">
            إضافة مدير
        </span>
    </a>
    <!-- end of float add button -->

    <div class="container">

        @if (count($admins) > 0)
        <!-- start of manager title section -->
        <div class="list-title py-3 pe-4 mt-5 mb-4">
            <div class="row text-center">
                <div class="col-lg-3">الإسم</div>
                <div class="col-lg-3">البريد الإلكتروني</div>
                <div class="col-lg-3">الحالة</div>
                <div class="col-lg-3">إجراء</div>
            </div>
        </div>
        <!-- start of manager title section -->

        <!-- start of manager list section -->
        <div id="list_content">

            {{-- loop throw all managers --}}
            @foreach ($admins as $admin)
            <div class="list-content py-4 pe-4 mt-4 mb-3" id="list_content">
                <div class="row justify-content-center align-items-center text-center">
                    <div class="col-lg-3 text-lg-left">
                        <div class="d-flex align-items-center justify-content-center justify-content-lg-start">
                            <img src="{{ asset('upload/profiles_photos/thumbs/' . $admin->photo) }}" alt="Logo" id="round-profile" class="img-fluid" />
                            <span class="pe-2 user-name">{{ $admin->name }}</span>
                        </div>
                    </div>
                    <div class="col-lg-3 user-email">{{ $admin->email }}</div>
                    <div class="col-lg-3 user-statue d-flex justify-content-center align-items-center">
                        <input type="checkbox" class="toggle-class" data-id="{{ $admin->id }}" id="{{ $admin->id }}" {{ $admin->locked ? 'checked' : '' }}>
                        <label for="{{ $admin->id }}"></label>
                        <span class="me-2"></span>
                    </div>
                    <div class="col-lg-3 user-pros">
                        <a class="delete_popup text-decoration-none" id="delete_popup" onclick="editAdmin({{ $admin->id }},'{{ $admin->name }}' , '{{ $admin->email }}')">
                            <i class="fa fa-pen "></i>
                        </a>
                        <a class="delete_popup text-decoration-none" id="delete_popup" onclick="deleteAdmin({{ $admin->id }})">
                            <i class="fa fa-trash-can pe-3"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <!-- start of manager list section -->

    </div>

    <!-- display a message if there is no manager  -->
    @else
    <div class="container text-center">

        @if(request()->route()->getName() === 'manage' && !request()->has('search'))
        <h1 class="mb-0 pb-0 pt-5 text-muted">لم يتم إضافة اي مدير حتى الآن !!</h1>
        @elseif(request()->route()->getName() === 'manage' && request()->has('search'))
        <h1 class="mb-0 pb-0 pt-5 text-muted">لا يوجد مدير بهذا الاسم !!</h1>
        @endif

        <img src="{{ asset('upload/No_data.svg') }}" class="img-fluid w-75 w">
    </div>
    @endif
</div>

{{-- add manager popup --}}
<div class="modal fade modal-md" id="add_manager" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="add_manager_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-4">
                <span class="icon-bordered fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-close"></i></span>
                <h5 class="modal-title text-center w-100" id="add_manager_label">إضافة مدير</h5>
            </div>
            <div class="modal-body  mx-5 mb-4">
                <form method="POST" id="manager_form">
                    @csrf

                    <div class="form-group">
                        <div class="col-12 mt-4 mb-3">
                            <label for="nameInput" class="form-label manager-name">إسم المستخدم</label>
                            <input type="text" class="form-control" name="name" id="nameInput" required placeholder="محمد شوابي" autofocus>
                            <span class="invalid-feedback" role="alert" id="nameError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="emailInput" class="form-label manager-email">البريد الإلكتروني</label>
                        <input type="email" class="form-control" name="email" id="emailInput" required placeholder="exsample@gmil.com" value="{{ old('email') }}">
                        <span class="invalid-feedback" role="alert" id="emailError">
                            <strong></strong>
                        </span>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="passwordInput" class="form-label manager-name">كلمة المرور</label>
                        <input type="password" class="form-control" name="password" id="passwordInput" required autocomplete="new-password">
                        <span class="invalid-feedback" role="alert" id="passwordError">
                            <strong></strong>
                        </span>
                    </div>

                    <div class="col-12">
                        <label for="password-confirm" class="form-label manager-confirm-pass">تأكيد كلمة المرور
                        </label>
                        <input type="password" class="form-control" name="password_confirmation" required id="password-confirm" autocomplete="new-password">
                        <div class="invalid-feedback"></div>
                    </div>

            </div>
            <div class="modal-footer  justify-content-evenly mb-4">
                <input type="submit" id="submit" class="save btn" value="حفظ" />
                <input type="reset" class="cancel btn btn-secondary" data-bs-dismiss="modal" value="إلغاء" />
            </div>
            </form>
        </div>
    </div>
</div>

{{-- edit manager popup --}}
<div class="modal fade modal-md" id="edit_manager" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="edit_manager_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header py-4">
                <span class="icon-bordered fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-close"></i></span>
                <h5 class="modal-title text-center w-100" id="edit_manager_label">تعديل مدير</h5>
            </div>
            <div class="modal-body  mx-5 mb-4">
                <form id="edit_manager_form">
                    @csrf
                    <input type="hidden" name="edit_admin_id" id="edit_admin_id">
                    <div class="form-group">
                        <div class="col-12 mt-4 mb-3">
                            <label for="nameEditInput" class="form-label manager-name">إسم المستخدم</label>
                            <input type="text" class="form-control" name="name" id="nameEditInput" required placeholder="محمد شوابي" autofocus>
                            <span class="invalid-feedback" role="alert" id="nameEditError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="emailEditInput" class="form-label manager-email">البريد الإلكتروني</label>
                        <input type="email" class="form-control" name="email" id="emailEditInput" required placeholder="exsample@gmil.com" value="{{ old('email') }}">
                        <span class="invalid-feedback" role="alert" id="emailEditError">
                            <strong></strong>
                        </span>
                    </div>

                    <div class="col-12 mb-3">
                        <label for="passwordEditInput" class="form-label manager-name">كلمة المرور</label>
                        <input type="password" class="form-control" name="password" id="passwordEditInput" autocomplete="new-password" placeholder="اترك الحقل فارغا اذا لم ترد التعديل">
                        <span class="invalid-feedback" role="alert" id="passwordEditError">
                            <strong></strong>
                        </span>
                    </div>

                    <div class="col-12">
                        <label for="password_confirmationEditInput" class="form-label manager-confirm-pass">تأكيد كلمة المرور
                        </label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmationEditInput" placeholder="تأكيد كلمة المرور الجديدة" autocomplete="new-password">
                        <span class="invalid-feedback" role="alert" id="password_confirmationEditError">
                            <strong></strong>
                        </span>
                    </div>

            </div>
            <div class="modal-footer  justify-content-evenly mb-4">
                <input type="submit" id="submitEdit" class="save btn" value="حفظ" />
                <input type="reset" class="cancel btn btn-secondary" data-bs-dismiss="modal" value="إلغاء" />
            </div>
            </form>
        </div>
    </div>
</div>


{{-- delete popup --}}
<div class="modal fade" tabindex="-1" id="delete_manager" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">حذف مدير</h5>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="delete" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="admin_id" id="admin_id">
                    <p class="text-center delete-text">هل انت متاكد من الحذف</p>
                </div>
                <div class="modal-footer justify-content-evenly">
                    <button type="submit" class="btn save" id="delete_btn">حذف</button>
                    <button type="button" class="btn btn-secondary cancel" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection