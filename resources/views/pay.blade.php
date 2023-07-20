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

    </style>
</head>
<body>
<div class="container">

    <div class="row">
        <div class="col-12 d-flex align-items-center justify-content-center" style="height: 100vh">
            <div class="card">
                <div class="card-body">

                    <div class="row">
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
                            <h3 class="text-dark mb-4">مبلغ قابل پرداخت : {{ number_format($transaction->amount) }}
                                تومان </h3>
                            @if($paymentsCount > 1)
                                <div style="padding: 1rem 0">
                                    <h4 class="text-dark mb-4">مبلغ قابل پرداخت در این مرحله : {{ number_format($remainingAmount > config("easy-payment.maxPaymentAmount") ? config("easy-payment.maxPaymentAmount") : $remainingAmount) }}
                                        تومان </h4>
                                    <div class="alert alert-info mb-3" style="font-size: 12pt;font-weight: bold">
                                        <strong>مهم ! : </strong>
                                        به دلیل اینکه مبلغ پرداختی شما بیشتر
                                        از {{ number_format(config("easy-payment.maxPaymentAmount")) }} تومان است باید آن را
                                        طی چند مرحله پرداخت کنید .
                                        لذا پس از هر پرداخت مجدد به این صفحه بر میگردید تا به صورت کامل جزییات پرداخت خود را
                                        ببینید
                                    </div>
                                </div>
                            @endif
                            <div class="note note-primary mb-3">
                                <strong>راهنما پرداخت:</strong> ابتدا یکی از درگاه های پایین را انتخاب کنید سپس دکمه
                                انتقال به درگاه پرداخت رو بزنید . بعد از تکمیل پرداخت حتما دکمه انتقال به سایت پذیرنده
                                رو زنید تا به سایت ما برگردید
                            </div>
                            <div class="note note-info mb-3">
                                <strong>توجه : </strong>در صورتی که پرداخت انجام شد و هزینه از حساب شما کسر شد ولی در
                                سایت ما پرداخت موفق نبود ، نهایتا تا 72 ساعت مبلغ پرداختی به حساب شما برمیگرده
                            </div>
                        </div>

                    </div>
                    <div class="row mt-2">
                        <div class="col-12 my-2">
                            <h4>لطفا یک درگاه را انتخاب کنید</h4>
                        </div>
                        <div class="col-12 col-lg-10">
                            <div class="row card-radio">
                                <div class="col-lg-3 col-6">
                                    <label>
                                        <input type="radio" name="shipping-type" class="card-input-element"
                                               value="zarinpal" checked/>
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
                                               value="payping"/>
                                        <div class="panel panel-default card-input">
                                            <div>
                                                <div class="panel-body">
                                                    <img src="{{ asset("vendor/easy-payment/payping.png") }}"
                                                         width="120px" alt="">
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>

                            </div>
                        </div>
                        <div class="col-12 col-lg-2 d-flex justify-content-end align-items-end">
                            <button class="btn btn-success" style="height: max-content">پرداخت</button>
                        </div>
                    </div>
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
</body>
</html>
