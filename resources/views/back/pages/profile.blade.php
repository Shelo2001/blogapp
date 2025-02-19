@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle)? $pageTitle:'Page Title Here')
@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<div class="page-header">
    <div class="row">
        <div class="col-md-12 col-sm-12">
            <div class="title">
                <h4>Profile</h4>
            </div>
            <nav aria-label="breadcrumb" role="navigation">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{route('admin.dashboard')}}">Home</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Profile
                    </li>
                </ol>
            </nav>
        </div>
    </div>
</div>

@livewire('admin.profile')
      
@endsection
@push('scripts')
    <script>
        $('input[type="file"][id="profilePictureFile"]').kropify({
        preview:'img#profilePicturePreview',
        viewMode:1,
        aspectRatio:1,
        cancelButtonText:'Cancel',
        resetButtonText:'Reset',
        cropButtonText:'Crop & update',
        processURL:'{{route('admin.update_profile_picture')}}',
        maxSize:2097152, //2MB
        showLoader:true,
        animationClass:'headShake', //headShake, bounceIn, pulse
        fileName:'profilePictureFile',
        success:function(data){
         if(data.status==1){
            Livewire.dispatch('updateTopUserInfo',[]);
            Livewire.dispatch('updateProfile',[]);

            Toastify({
                text: data.message,
                duration: 2000,
            }).showToast();
         }
            },
          errors:function(error, text){
            Toastify({
                text: data.message,
                duration: 3000,
            }).showToast();
          },
        });
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
@endpush