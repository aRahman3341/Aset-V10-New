<header class="fixed-top bg-white">
    <div class="container">
        <div class="row">
            <div class="col-3 col-md-1 text-end">
                <img src="{{ asset('assets/img/PUPR.png') }}" width="70" height="70" alt="">
            </div>
            <div class="col-9 col-md-8 text-start">
                <span>
                    <span>KEMENTERIAN PEKERJAAN UMUM DAN PERUMAHAN RAKYAT</span> <br>
                    <span>
                        <b style="color : #354571;">
                            DIREKTORAT JENDERAL CIPTA KARYA<br>
                            DIREKTORAT BINA TEKNIK PERMUKIMAN DAN PERUMAHAN <br>
                            BALAI SAINS BANGUNAN
                        </b>
                    </span>
                </span>
            </div>
            <div class="col-md-3 text-end" style="background: linear-gradient(to right, rgb(255, 255, 255), rgba(0, 0, 0, 0)), url('{{ asset('assets/img/KementrianRI.jpg') }}') ; background-size: auto 100%;">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @include('layouts/navbar')
        </div>
    </div>
</header>

{{-- Spacer untuk fixed header: logo-area (~80px) + navbar (~50px) = 135px --}}
<div style="height: 135px;"></div>