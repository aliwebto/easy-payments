<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>پرداخت آنلاین</title>
    <!-- Font Awesome -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        rel="stylesheet"
    />
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"
        rel="stylesheet"
    />
    <!-- MDB -->
    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.css"
        rel="stylesheet"
    />
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet"
          type="text/css"/>

    <style>
        body {
            font-family: Vazirmatn, sans-serif;
        }

        .note {
            border-left: none;
            border-right: 6px solid;
        }

        .note-primary {
            background-color: #dfe7f6 !important;
            border-color: #376fc8 !important;
        }

        .note-info {
            background-color: #def1f7 !important;
            border-color: #2686a6 !important;
        }

        .card-radio {
            font-size: 10pt;
            font-weight: bold;
        }

        .card-radio label {
            width: 100%;
            height: 100%;
        }

        .card-radio .card-input-element {
            display: none;
        }

        .card-radio .card-input {
            padding: 1rem;
            text-align: center;
            border-radius: 12px;
            border: 2px solid #eee;
            min-height: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-radio .panel {
            height: 100%;
        }

        .card-radio .card-input:hover {
            cursor: pointer;
        }

        .card-radio .card-input-element:checked + .card-input {
            border: 2px solid #316cf4;
        }

        .successful_mask {
            position: absolute;
            z-index: 9999;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #ffffff4d;
            top: 0;
        }

        .successful_mask .icon {
            display: block;
            width: 100%;
            text-align: center;
        }

        .successful_mask .title {
            display: block;
            width: 100%;
            text-align: center;
            font-size: 18pt;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="row">
        <div class="col-12 d-flex align-items-center justify-content-center" style="min-height: 100vh">
            <div class="card">
                <div class="card-body" style="position: relative">
                    @if (request()->has("status"))
                        <div class="alert alert-{{ request()->get("status") }}">
                            {{ request()->get("message") }}
                        </div>
                    @endif
                    <div @if(!is_null($transaction->paid_at)) style="filter: blur(8px);" @endif class="row">

                        <div class="col-lg-6 col-12">
                            <h3 class="text-dark mb-4">جزییات پرداخت
                                @if($paymentsCount > 1)
                                    <span style="font-size: 13pt;">( مرحله {{ $paymentsCount - $remainingPaymentsCount + 1 }} از {{ $paymentsCount }} )</span>
                                @endif
                            </h3>

                            <div class="note note-primary">
                                <p class="d-flex justify-content-between">
                                    <span>نام وب سایت مرجع :</span>
                                    <span>{{ config("easy-payment.site-name") }}</span>
                                </p>
                                @if(!is_null($transaction->specificCard))
                                    <p class="d-flex justify-content-between">
                                        <span>کارت پرداختی :</span>
                                        <span>{{ $transaction->specificCard }}</span>
                                    </p>
                                @endif

                                <p class="d-flex justify-content-between">
                                    <span>شناسه پرداخت سایت :</span>
                                    <span>{{ $transaction->transaction_uuid}}</span>
                                </p>
                                <p class="d-flex justify-content-between">
                                    <span>شماره پیگیری :</span>
                                    <span>{{ $transaction->id }}</span>
                                </p>
                                <p class="d-flex justify-content-between">
                                    <span>توضیحات :</span>
                                    <span>{{ $transaction->description }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-lg-6 col-12">

                            @if($paymentsCount > 1)
                                <h3 class="text-dark mb-4">مبلغ قابل پرداخت
                                    : {{ number_format($transaction->amount) }}
                                    ریال </h3>
                            @else
                                <h3 class="text-dark mb-4">مبلغ قابل پرداخت
                                    : {{ number_format($transaction->amount - $transaction->paidAmount) }}
                                    ریال </h3>
                            @endif
                            @if($paymentsCount > 1)
                                <div style="padding: 1rem 0">
                                    <h4 class="text-dark mb-4">مبلغ قابل پرداخت در این مرحله
                                        : {{ number_format($remainingAmount > config("easy-payment.maxPaymentAmount") ? config("easy-payment.maxPaymentAmount") : $remainingAmount) }}
                                        ریال </h4>
                                    <div class="alert alert-info mb-3"
                                         style="font-size: 12pt;font-weight: bold">
                                        <strong>مهم ! : </strong>
                                        به دلیل اینکه مبلغ پرداختی شما بیشتر
                                        از {{ number_format(config("easy-payment.maxPaymentAmount")) }} تومان
                                        است باید
                                        آن را
                                        طی چند مرحله پرداخت کنید .
                                        لذا پس از هر پرداخت مجدد به این صفحه بر میگردید تا به صورت کامل جزییات
                                        پرداخت
                                        خود را
                                        ببینید
                                    </div>
                                </div>
                            @endif
                            <div class="note note-primary mb-3">
                                <strong>راهنما پرداخت:</strong> ابتدا یکی از درگاه های پایین را انتخاب کنید سپس
                                دکمه
                                انتقال به درگاه پرداخت رو بزنید . بعد از تکمیل پرداخت حتما دکمه انتقال به سایت
                                پذیرنده
                                رو زنید تا به سایت ما برگردید
                            </div>
                            <div class="note note-info mb-3">
                                <strong>توجه : </strong>در صورتی که پرداخت انجام شد و هزینه از حساب شما کسر شد
                                ولی در
                                سایت ما پرداخت موفق نبود ، نهایتا تا 72 ساعت مبلغ پرداختی به حساب شما برمیگرده
                            </div>
                        </div>

                    </div>
                    <div @if(!is_null($transaction->paid_at)) style="filter: blur(8px);" @endif class="row mt-2">
                        <div class="col-12 my-2">
                            <h4>لطفا یک درگاه را انتخاب کنید</h4>
                        </div>
                        <div class="col-12 col-lg-10">
                            <div class="row card-radio">
                                <div class="col-lg-3 col-6">
                                    <label>
                                        <input type="radio" name="shipping-type" class="card-input-element"
                                               value="zarinpal"/>
                                        <div class="panel panel-default card-input">
                                            <div>
                                                <div class="panel-body">
                                                    <img src="{{ asset("vendor/easy-payment/zarinpal.png") }}"
                                                         width="120px" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-lg-3 col-6">
                                    <label>
                                        <input type="radio" name="shipping-type" class="card-input-element"
                                               value="zibal"/>
                                        <div class="panel panel-default card-input">
                                            <div>
                                                <div class="panel-body">
                                                    <img src="{{ asset("vendor/easy-payment/zibal.svg") }}"
                                                         width="120px" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="col-12 col-lg-2 d-flex justify-content-end align-items-end">
                            <button class="btn btn-success" id="payBtn" style="height: max-content">پرداخت
                            </button>
                        </div>
                    </div>
                    @if(!is_null($transaction->paid_at))
                        <div class="successful_mask">
                            <div>
                            <span class="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="120px" height="120px"><path
                                    fill="#c8e6c9"
                                    d="M36,42H12c-3.314,0-6-2.686-6-6V12c0-3.314,2.686-6,6-6h24c3.314,0,6,2.686,6,6v24C42,39.314,39.314,42,36,42z"/><path
                                    fill="#4caf50"
                                    d="M34.585 14.586L21.014 28.172 15.413 22.584 12.587 25.416 21.019 33.828 37.415 17.414z"/></svg>
                        </span>
                                <span class="title">پرداخت شما با موفقیت تکمیل شده است</span>
                                <div class="text-center"><a href="{{ config("easy-payment.returnAfterComplete") }}" class="btn btn-success">بازگشت به سایت</a></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table align-middle mb-0 bg-white">
                        <thead class="bg-light">
                        <tr>
                            <th>درگاه</th>
                            <th>مبلغ</th>
                            <th>تاریخ</th>
                            <th>ساعت</th>
                            <th>وضعیت</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($transaction->payments) > 0)
                            @foreach($transaction->payments as $payment)
                                <tr>
                                    <td>
                                        {{ $payment->gateway_name }}
                                    </td>
                                    <td>
                                        {{ number_format($payment->amount) }}
                                    </td>
                                    <td>
                                        {{ $payment->created_at->format("Y/m/d") }}
                                    </td>
                                    <td>{{ $payment->created_at->format("H:i") }}</td>
                                    <td>
                                        @if($payment->paid_at)
                                            <span class="badge badge-success rounded-pill d-inline">موفق</span>
                                        @else
                                            <span class="badge badge-danger rounded-pill d-inline">ناموفق</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5">موردی یافت نشد</td>
                            </tr>
                        @endif


                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- MDB -->
<script
    type="text/javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.4.0/mdb.min.js"
></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"
        integrity="sha512-uMtXmF28A2Ab/JJO2t/vYhlaa/3ahUOgj1Zf27M5rOo8/+fcTUVH0/E0ll68njmjrLqOBjXM3V9NiPFL5ywWPQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-start',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    })
    var payBtn = document.getElementById("payBtn");
    payBtn.addEventListener("click", function () {
        payBtn.setAttribute("disabled", "disabled");
        payBtn.innerHTML = '<div class="spinner-border spinner-border-sm text-light" role="status"> <span class="visually-hidden">Loading...</span> </div>';
        pay();
    })

    function pay() {
        let gatewayRadio = document.getElementsByName("shipping-type");
        var gateway = "";
        for (let radio of gatewayRadio) {
            if (radio.checked) {
                gateway = radio.value;
            }
        }

        if (gateway == "") {


            Toast.fire({
                icon: 'error',
                title: 'لطفا ابتدا یک درگاه پرداخت را انتخاب کنید'
            })
            payBtn.removeAttribute("disabled");
            payBtn.innerHTML = "پرداخت";
            return false;
        }

        let getRedirectURL_API = "{{ route("easy-payment.pay",["uuid"=>base64_encode($transaction->transaction_uuid)]) }}";

        axios.get(getRedirectURL_API + "&gateway_name=" + gateway).then(resp => {
            setTimeout(function (){
                window.location = resp.data.payment_url;
            },2000);
            Swal.fire({
                title: '<strong>در حال انتقال به درگاه پرداخت</u></strong>',
                icon: 'info',
                html:
                    'در حال انتقال به درگاه پرداخت هستید، در صورتی که به صورت خودکار منتقل نشدید ' +
                    '<a href="' + resp.data.payment_url + '">اینجا</a> ' +
                    'کلیک کنید',
                showCloseButton: false,
                showCancelButton: false,
                showConfirmButton: false,
                allowOutsideClick: false,
                focusConfirm: true
            })
        }).catch(function (error) {
            console.log(error);
            Toast.fire({
                icon: 'error',
                title: error.response.data.message
            })
            payBtn.removeAttribute("disabled");
            payBtn.innerHTML = "پرداخت";

        });


    }
</script>
</body>
</html>
