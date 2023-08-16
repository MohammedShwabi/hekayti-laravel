@extends('layouts.app')

@section('content')
<div class="profile-page">
    <div class="container">

        <!-- start of loading overlay element -->
        <div id="loading-overlay">
            <div class="spinner"></div>
        </div>
        <!-- end of loading overlay element -->

        <div class="row">

            <!-- start of page title -->
            <div class="row m-5">
                <div class="page-title">
                    <span class="dash-profile"></span>
                    <span class="title">
                        المعلومات الشخصية
                    </span>
                </div>
            </div>
            <!-- end of page title -->

            <!-- start of profile card  -->
            <div class="row profile-card mt-3 mb-3">

                <!-- profile photo section -->
                <div class="col-lg-6 ">
                    <!-- upload photo error message -->
                    <div id="error-image-message" class="alert-danger rounded text-center m-2"></div>
                    <div class="profile-img">
                        <span id="edit_profile_photo" onclick="editMedia('image', 'editProfilePhoto')">
                            <i class="fa fa-camera camera-icon"></i>
                        </span>
                        <img class="img-fluid shadow-sm" height="300px" id="profile_photo" src="{{ asset('storage/upload/profiles_photos/' . Auth::user()->image) }}" alt="">
                    </div>
                </div>

                <!-- profile data section  -->
                <div class="col-lg-6 ">
                    <div class="profile-data">
                        <div class="mx-5 mb-4">
                            <div class="form-group">
                                <div class="col-12 mt-4 mb-3">
                                    <label for="user_name" class="form-label user-name">الإسم</label>
                                    <div class="row justify-content-center align-items-center gx-2">
                                        <div class="col-8 ">
                                            <div class="data-label user-name">{{ Auth::user()->name }}</div>
                                        </div>
                                        <div class="col-4 ">
                                            <a class="btn change-pass-btn edit_name" id="edit_name" role="button" data-bs-toggle="modal" data-bs-target="#edit_name_pop">
                                                <i class="fa fa-pen ps-2"></i>
                                                <span>
                                                    تعديل
                                                </span>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="user_email" class="form-label user-email">البريد الإلكتروني</label>
                                <div class="data-label user-email">{{ Auth::user()->email }}</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="user_pass" class="form-label user-pass">كلمة المرور</label>
                                <div class="data-label user-pass">**************</div>
                            </div>
                        </div>
                        <a class="btn update-profile-btn" role="button" data-bs-toggle="modal" data-bs-target="#change_password">
                            <span>
                                تغيير كلمة المرور
                            </span>
                            <i class="fa fa-pen pe-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- end of profile card   -->

        </div>
    </div>
</div>


{{-- edit name popup --}}
<div class="modal fade" tabindex="-1" id="edit_name_pop" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل الأسم</h5>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit_name_form">
                @csrf
                <div class="modal-body mx-5 mb-4">
                    <div class="form-group">
                        <div class="col-12 mt-4 mb-3">
                            <label for="usernameEditInput" class="form-label manager-name">إسم المستخدم</label>
                            <input type="text" required class="form-control" name="username" id="usernameEditInput" placeholder="محمد شوابي" autofocus value="{{ Auth::user()->name }}">
                            <span class="invalid-feedback" role="alert" id="usernameEditError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-evenly">
                    <button type="submit" class="btn save" id="delete_btn">حفظ</button>
                    <button type="button" class="btn btn-secondary cancel" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- change_password popup --}}
<div class="modal fade modal-md" id="change_password" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="change_password_label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header py-4">
                <span class="icon-bordered fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-close"></i></span>
                <h5 class="modal-title text-center w-100" id="change_password_label">تغيير كلمة المرور</h5>
            </div>
            <form id="change_pass">
                @csrf
                <div class="modal-body mx-5 mb-4">
                    <div class="form-group">
                        <div class="col-12 mt-4 mb-3">
                            <label for="old_password" class="form-label old-pas">كلمة المرور القديمة</label>
                            <input type="password" required class="form-control" name="old_password" id="old_passwordInput">
                            <span class="invalid-feedback" role="alert" id="old_passwordError">
                                <strong></strong>
                            </span>
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label for="new_password" class="form-label manager-name new-password">كلمة المرور
                            الجديدة</label>
                        <input type="password" required class="form-control" name="new_password" id="new_passwordInput">
                        <span class="invalid-feedback" role="alert" id="new_passwordError">
                            <strong></strong>
                        </span>
                    </div>
                    <div class="col-12">
                        <label for="new_password_confirmationInput" class="form-label manager-name">تأكيد كلمة المرور
                            الجديدة</label>
                        <input type="password" required class="form-control" name="new_password_confirmation" id="new_password_confirmationInput">
                        <span class="invalid-feedback" role="alert" id="new_password_confirmationError">
                            <strong></strong>
                        </span>
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

@endsection