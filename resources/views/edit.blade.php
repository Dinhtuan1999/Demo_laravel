@extends('backend.layout.layout')
@section('title','オークション変更')
@section('content')
    <style>
        /*.modal:nth-of-type(even) {*/
        /*    z-index: 1052 !important;*/
        /*}*/

        /*.modal-backdrop.show:nth-of-type(even) {*/
        /*    z-index: 1051 !important;*/
        /*}*/

        .btn-confirm {
            border: none;
        }

        .list-box {
            height: 260px;
            overflow-y: scroll;
        }

        .footer-create-box, .body-create-box {
            padding-bottom: 0;
            padding-top: 0;
        }

        .header-create-box {
            padding-bottom: 0px;
        }

        /*.form-create-box {*/
        /*    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.25);*/
        /*}*/


        @media (min-width: 992px) {
            .modal-lg {
                max-width: 90%;
            }

            .modal-lg-list {
                max-width: 80%;
            }
        }

        .error {
            color: red;
            /*background-color: #acf;*/
        }

        .form-group-modal {
            margin-bottom: 2.25em;
        }

        #button_list_box {
            border: none;
            background: white;
        }
    </style>

    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="feather icon-clipboard bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>オークション変更</h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="page-header-breadcrumb">
                    <ul class=" breadcrumb breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="{!!route('admin.index')!!}"><i class="feather icon-home">ホーム</i></a>
                        </li>
                        <li class="breadcrumb-item">
                            オークション変更
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>オークション日</h5>
                    </div>
                    <div class="card-block">
                        <form method="POST" action="{{ route('auction.day.update') }}"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">オークションID <span class="cle">*</span></label>
                                <div class="col-sm-6">
                                    <input type="hidden" id="id_auction" value="{{$auction_day->id}}">
                                    <input name="title" type="text" class="form-control" id="title"
                                           value="{{$auction_day->title}}">
                                    <div class="alert alert-danger">{{ $errors->first('title') }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">カテゴリーID<span class="cle">*</span></label>
                                <div class="col-sm-6">
                                    <select class="js-example-basic-single col-sm-12 category_id" name="category_id">
                                        <option value="">カテゴリーIDを選択してください。</option>
                                        @if(!is_null($categories))
                                            @foreach($categories as $key =>$value)
                                                <option
                                                    {{ $auction_day['category_id'] ==  $value->id ? 'selected' : ''}} value="{{ $value['id'] }}">{{ $value['title'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    {!! showErrors($errors ,'category_id') !!}
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">開催日 <span class="cle">*</span></label>
                                <div class="col-sm-6">
                                    <input class="form-control start_date" type="date" name="start_date"
                                           value="{{ Carbon\Carbon::parse($auction_day->start_date)->format('Y-m-d') }}">
                                    <div class="alert alert-danger">{{ $errors->first('start_date') }}</div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">保留交渉種別<span class="cle">*</span></label>
                                <div class="col-sm-6">
                                    <div class="form-radio m-b-30">
                                        <div class="radio radiofill radio-default radio-inline">
                                            <label>
                                                <input type="radio" name="reply_negotiation" value="0" id="reply_negotiation"
                                                       @if($auction_day->reply_negotiation == 0) checked @endif>
                                                <i class="helper"></i>再々交渉を使用しない
                                            </label>
                                        </div>
                                        <div class="radio radiofill radio-primary radio-inline">
                                            <label>
                                                <input type="radio" name="reply_negotiation" value="1"  id="reply_negotiation"
                                                       @if($auction_day->reply_negotiation == 1) checked @endif>
                                                <i class="helper"></i>再々交渉を使用する
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">ステータス<span class="cle">*</span></label>
                                <div class="col-sm-6">
                                    <div class="form-radio m-b-30">
                                        <div class="radio radiofill radio-default radio-inline">
                                            <label>
                                                <input type="radio" name="status" @if($auction_day->status == 0) checked
                                                       @endif  @if($auction_day->status == 2) disabled @endif value="0">
                                                <i class="helper"></i>開始前
                                            </label>
                                        </div>
                                        <div class="radio radiofill radio-primary radio-inline">
                                            <label>
                                                <input type="radio" name="status" @if($auction_day->status == 1) checked
                                                       @endif  @if($auction_day->status == 2) disabled
                                                       @endif   value="1">
                                                <i class="helper"></i>開催中
                                            </label>
                                        </div>
                                        <div class="radio radiofill radio-danger radio-inline">
                                            <label>
                                                <input type="radio" name="status" @if($auction_day->status == 2) checked
                                                       @endif value="2">
                                                <i class="helper"></i>終了
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label"> 箱数 <span class="cle">*</span></label>
                                <div class="col-sm-2">
                                    <input class="form-control " type="text" id="list_box_num" min=""
                                           name="list_box_num" value="">
                                    <input class="form-control " type="hidden" id="data_box_num" min=""
                                           name="data_box_num[]" value="">
                                </div>
                                <div class="col-sm-2">
                                    @if($auction_day->status == 1)
                                        <button type="button" class="btn-success showModalCreateBox" data-toggle="modal"
                                                id="showModalCreateBox"
                                                data-target="#myModal" disabled>
                                            箱番号のモーダルを表示
                                        </button>
                                    @else
                                        <button type="button" class="btn-success showModalCreateBox" data-toggle="modal"
                                                id="showModalCreateBox"
                                                data-target="#myModal" >
                                            箱番号のモーダルを表示
                                        </button>
                                    @endif


                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-lg-6 col-xs-12">
                                    <button type="submit" style="float:right"
                                            class="btn waves-effect waves-light btn-info btn-square button_submit_auction">保存
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--------------------------modal create box code ------------------------>
    <div class="modal" id="myModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header  header-create-box">
                    <div class="col-sm-5 col-form-label">
                    </div>

                    <div class="col-sm-7 col-form-label">
                        <div class="col-sm-1 float-right">
                            <button type="button" class="close " id="close" data-dismiss="modal"
                                    aria-hidden="true">×
                            </button>
                        </div>

                        <div class="col-sm-11 text-center show_list"
                             style="padding: 0px;margin: 0px">
                            <a data-toggle="modal" href="#myModal2" class="btn btn-primary">一覧を確認</a>
                        </div>
                    </div>

                </div>

                <div class="container"></div>
                <div class="modal-body  body-create-box">
                    <div class=" row">
                        <div class="col-sm-5 col-form-label list-box" id="list-box"></div>
                        <div class="col-sm-7 col-form-label form-create-box">
                            <div class="form-group-modal row ">
                                <label class="col-sm-3 col-form-label ">会員を選択 <span
                                        class="cle">*</span></label>
                                <div class="col-sm-4" style="padding-right: 5px">

                                    <select class=" col-sm-12 changeMember js-select2"
                                            name="buyerId">
                                        <option value="">入札者IDを選択してください。</option>
                                        @foreach(@$member as $key => $member)
                                            <option value="{{ $member->buyer_id }}"
                                                    data-idMenber="{{$member->id}}"
                                                    data-idItem="{{$value->id}}"
                                                    data-name="{{$member->name}}">{{ $member->buyer_id.': '.$member->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-4" style="padding-left: 5px; height: 44px">
                                    <textarea class="form-control addName name" id="addName" min="" name="" value=""
                                              style="height: 100%" readonly></textarea>
                                    <div class="alert alert-danger user_err"></div>
                                </div>
                            </div>
                            <div class="form-group-modal row">
                                <label class="col-sm-3  col-form-label">範囲を選択</label>
                                <div class="col-sm-3">
                                    <input class="form-control " type="number" id="min_box"
                                           min="" name="" value="">
                                </div>
                                <span>~</span>

                                <div class="col-sm-3" style="padding-right: 22px">
                                    <input class="form-control " type="number" id="max_box"
                                           min="" name="" value="">
                                    <div class="alert alert-danger max_box_err"></div>
                                </div>
                            </div>
                            <div class="form-group-modal row">
                                <label class="col-sm-3  col-form-label">箱数を選択 <span
                                        class="cle">*</span></label>
                                <div class="col-sm-5">
                                    <input class="form-control " type="number" id="box_number"
                                           min="" name="" value="">
                                    <div class="alert alert-danger box_number_err"></div>
                                </div>
                                <div class="col-sm-1" style="padding: 10px 0"><span>箱</span>
                                </div>
                            </div>
                            <div class="form-group-modal row">
                                <div class="col-md-12 text-center">
                                    <button type="button" class="btn-info btn-confirm"
                                            id="confirm">
                                        決定
                                    </button>
                                </div>
                            </div>
                            <div class="form-group-modal row"
                                 style="padding-left: 100px; margin-bottom: 0px;">
                                <label class="col-sm-3  col-form-label">箱番号 <span
                                        class="cle">*</span></label>
                                <div class="col-sm-5">
                                    <input class="form-control box_list" type="text"
                                           id="box_list" name="box_list[]"
                                           value="{{ old('box_list[]') }}" readonly>
                                           <input class="form-control " type="hidden" id="array_box_num" min=""
                                           name="array_box_num" value="">
                                </div>
                                <div class="col-sm-3 check_quantity" style="display: none">
                                    <p style="padding: 0;margin: 0">選択されています</p>
                                    <div id="quantity">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer footer-create-box">
                    <div class="col-sm-5 col-form-label"></div>
                    <div class="col-sm-7 col-form-label">

                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-primary btn_register">
                                登録
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!--------------------------modal list box code ------------------------>
    <div class="modal" id="myModal2" data-backdrop="static">
        <div class="modal-dialog modal-lg-list">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-title col-md-12  text-center">
                        <input type="text" style="width: 300px; border-radius: 6px" class="search_list_box"
                               placeholder="検索..." value="">
                        <button class="btn btn-primary btn_search" type="button"
                                style="padding-bottom: 0;padding-top: 0; border-radius: 4px">検索
                        </button>
                        <button class="btn btn-success btn_reset" type="button"
                                style="padding-bottom: 0;padding-top: 0; border-radius: 4px">戻る
                        </button>
                        <button type="button" class="close" data-dismiss="modal"
                                aria-hidden="true">×
                        </button>

                    </div>
                </div>
                <div class="container">
                    <div id="arrPrint">

                    </div>
                </div>
                <div class="modal-body">
                    <table id="" class="table table-striped table-bordered nowrap">
                        <thead>
                        <tr>
                            <th>Box Code</th>
                            <th>会員ID</th>
                            <th>会員名</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody id="table_list_box">

                        @foreach($box_number as $data)
                            <tr id="box" class="box_{{$data->id}}">
                                <td>{{$data->box}}</td>
                                <td>{{$data->member_id}}</td>
                                <td>{{$data->member_name}}</td>
                                <td>
                                    <a id="remove_box"  class="remove_box_{{$data->box}} " onClick='deleteBoxExist({{$data->id}})'>
                                        <i  class= "feather icon-trash-2 f-w-600 f-16 text-c-red"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js"></script>

    <script>

        $('#start_date').on('change', function () {
            var start_date = $(this).val();
            $('#end_date').prop('min', function () {
                return start_date;
            })
        });

        $('.close1').click(function () {
            $(event.target).modal('hide');
        })


        $('#list_box_num').change(function () {

            if ($('#list_box_num').val()) {
                $('#showModalCreateBox').prop('disabled', false)
            } else {
                $('#showModalCreateBox').prop('disabled', true)
            }

            createListBox();
        });

        function createListBox() {
            let list_box_num = $('#list_box_num').val();
            let soft;
            let arr = [];
            let check = 10;
            soft = "<table border='1' width='100%' cellspacing='0' cellpadding='2' id='list_number'>";
            for (let i = 1; i <= list_box_num; i++) {
                arr.push(i);
                if (i == check) {
                    soft += "<tr>";
                    for (const element of arr) {
                        soft = soft + "<td style='width: 40px;height: 40px; text-align: center; background: white;' id='style_" + element + "'><button type='button' id='button_list_box' onClick='checkNum(" + element + ")'  class='element_" + element + "'>" + element + "</button></td>";
                    }
                    soft = soft + "</tr>";
                    check = check + 10;
                    arr = [];
                }
            }
            soft += "<tr>";
            for (const element of arr) {
                soft = soft + "<td style='width: 40px;height: 40px; text-align: center; background: white;' id='style_" + element + "'><button type='button' id='button_list_box' onClick='checkNum(" + element + ")'  class='element_" + element + "'>" + element + "</button></td>";
            }
            if (arr.length < 10) {
                var notArr = 10 - arr.length;
                for (let i = 0; i < notArr; i++) {
                    soft = soft + "<td style='height: 40px; text-align: center; background: white'></td>";
                }
            }
            soft = soft + "</tr>";
            soft = soft + "</table>";
            $('#list-box').append(soft);

        }

        $('#box_number').change(function () {
            let quantity_max = $(this).val() ? $(this).val() : 0;
            let quantity_min = 0;
            $('.box_list').val('');
            appendQuantityBox(quantity_min, quantity_max);
        })

        $('.changeMember').change(function () {
            let buyerId = $(this).val()
            let name = $(this).find(':selected').data('name');
            document.getElementById("addName").value = name;

        })

        $(' .js-select2').select2({
            tags: true,
            // placeholder: "Select an Option",
            // allowClear: true,
            width: '100%'
        });

        $('#start_date').on('change', function () {
            var start_date = $(this).val();
            $('#end_date').prop('min', function () {
                return start_date;
            })
        });

        $('#close ').click(function () {
            $('#list_number').remove();
        })

        $('#confirm').click(function () {
            let min = $('#min_box').val() ? $('#min_box').val() : 1;
            let max = $('#max_box').val() ? $('#max_box').val() : $('#list_box_num').val();
            let num = $('#box_number').val() ? $('#box_number').val() - 1 : 0;
            let name = $('#addName').val();   
            var data_exist_box= $('#array_box_num').attr('data-array-box');


            num === 0 ? $('.box_number_err').html('入力してください') : $('.box_number_err').css('display', 'none');
            name === '' ? $('.user_err').html('会員を選択してください') : $('.user_err').css('display', 'none');

            if (name && num) {
                $('.box_number_err').css('display', 'none');
                $('.max_box_err').css('display', 'none')
                $('.user_err').css('display', 'none');
                let arr = [];
                if(data_exist_box){
                    while (arr.length <= num) {

                        let r = generateRandom(min, max);

                        if (arr.indexOf(r) === -1 &&  JSON.parse(data_exist_box).indexOf(r) === -1) arr.push(r);

                    }

                }
                else {
                    while (arr.length <= num) {

                        let r = generateRandom(min, max);

                        if (arr.indexOf(r) === -1) arr.push(r);

                    }
                }
                
                
                
                document.getElementById("box_list").value = arr;

                if (arr.length === num + 1) {
                    appendQuantityBox(arr.length, num + 1);
                }

            }

        })

        function generateRandom(min, max) {
            min = Math.ceil(min);
            max = Math.floor(max);
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        $('#formCreateAuctionDay').validate({
            rules: {
                list_box_num: {
                    required: true,
                },
            },
            messages: {
                list_box_num: {
                    required: "出品番号を入力してください。",
                },
            }
        });

        let arrCheckNum = [];

        function checkNum(num) {

            let quantity = $('#box_number').val();

            if (quantity && arrCheckNum.length < quantity) {

                let check_num = $('td').hasClass('check_' + num);
                if (check_num) {

                    let index = arrCheckNum.indexOf(num);
                    arrCheckNum.splice(index, 1);

                    document.getElementById("style_" + num).style.backgroundColor = "white";
                    $(".element_" + num).css('background', 'white');
                    $("#style_" + num).removeClass('check_' + num)

                    document.getElementById("box_list").value = arrCheckNum;

                    appendQuantityBox(arrCheckNum.length, quantity)

                } else {
                    document.getElementById("style_" + num).style.backgroundColor = "#CCFFFF";
                    $(".element_" + num).css('background', '#CCFFFF');
                    $("#style_" + num).addClass('check_' + num)

                    if (arrCheckNum.indexOf(num) === -1) arrCheckNum.push(num);

                    document.getElementById("box_list").value = arrCheckNum;

                    appendQuantityBox(arrCheckNum.length, quantity)
                    if (arrCheckNum.length === quantity) {
                        $('.check_quantity').css('color', 'green');
                    }
                }

            } else {
                let check_num = $('td').hasClass('check_' + num);

                if (check_num) {
                    let index = arrCheckNum.indexOf(num);
                    arrCheckNum.splice(index, 1);

                    document.getElementById("style_" + num).style.backgroundColor = "white";
                    $(".element_" + num).css('background', 'white');
                    $("#style_" + num).removeClass('check_' + num)
                    document.getElementById("box_list").value = arrCheckNum;
                    appendQuantityBox(arrCheckNum.length, quantity)
                    if (arrCheckNum.length === quantity) {
                        $('.check_quantity').css('color', 'green');
                    }
                }
            }
        }


        $('.btn_register').click(function () {
            arrCheckNum = [];
            let name = $('.name').val();
            let id = $('.changeMember').val();
            let box = $('.box_list').val();
            let quantity = $('#box_number').val();
            let arr_box = box.split(",");
            arr_box = arr_box.sort(function (a, b) {
                return a - b;
            });

            if (arr_box.length == quantity) {
                for (let i = 0; i <= quantity - 1; i++) {
                    let html = '';
                    html += "<tr id='box'>";
                    html += "<td>" + arr_box[i] + "</td>";
                    html += "<td>" + id + "</td>"
                    html += "<td>" + name + "</td>"
                    html += "<td><a  id='remove_box'  class='remove_box_" + arr_box[i] + "'  onClick='deleteBox(" + arr_box[i] + "," + arr_box[i] + id + ")'   ><i  class='feather" + " icon-trash-2" + " f-w-600" + " f-16 " + "text-c-red'></a></i></td>"
                    html += "</tr>";
                    $('#table_list_box').append(html);

                    document.getElementById("style_" + arr_box[i]).style.backgroundColor = "#DDDDDD";
                    $('.element_' + arr_box[i]).css('background', '#DDDDDD');
                    $('.element_' + arr_box[i]).attr('disabled', true);

                    let object = {"id": arr_box[i] + id, "box": arr_box[i], "member_id": id, "name": name};
                    updateDataBox(object);
                }
            }


        })

        // remove tr
        $(document).on('click', '#remove_box', function () {
            $(this).closest('#box').remove();

        });

        $('.show_list').on('click', function () {

            $('.box_list').val('');
            $('.check_quantity').hide();
            $('#box_number').val('');
            $('#min_box').val('');
            $('#max_box').val('');
        })

        function appendQuantityBox(min, max) {
            $('.check_quantity').show();

            $('#quantity_box').remove();

            let html = '';
            html += "<p id='quantity_box'>" + min + " / " + max + " mã box</p>";
            $('#quantity').append(html);
        }

        function deleteBox(number, id) {

            var data = JSON.parse($('#data_box_num').attr('data-array'));

            var index_box = data.findIndex(item => parseInt(item.id) === id);

            data.splice(index_box, 1);

            jQuery('#data_box_num').attr('data-array', JSON.stringify(data));


            $('.element_' + number).css('background', 'white');
            $('.element_' + number).attr('disabled', false);
            $('#style_' + number).css('background', 'white');
            let quantity_box = $('#box_number').val() - 1;
            $('#box_number').val(quantity_box);

        }

        let arr_data_box = [];
        let array_exist_box = [];

        function updateDataBox(object) {
            arr_data_box.push(object)
            array_exist_box.push(object.box);

            jQuery('#data_box_num').attr('data-array', JSON.stringify(arr_data_box));
            // $('#data_box_num').val(arr_data_box);
            jQuery('#array_box_num').attr('data-array-box', JSON.stringify(array_exist_box));


        }


        $('.button_submit_auction').click(function () {
            // $.LoadingOverlay("show");
            let data_box = $('#data_box_num').data('array') ? $('#data_box_num').data('array') : null;
            let start_date = $('.start_date').val();
            let title = $('#title').val();
            let category_id = $('.category_id').val();
            let reply_negotiation = $('input[name="reply_negotiation"]:checked').val();
            let id = $('#id_auction').val();
            let status = $('input[name="status"]:checked').val();

        

            // if ($('#reply_negotiation').prop("checked")) {
            //     reply_negotiation = $('#reply_negotiation').val();
            // } else {
            //     reply_negotiation = 1;
            // }


            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });

            $.ajax({
                url: '{{ route('auction.day.update') }}',
                method: "POST",
                data: {
                    "data_box": data_box,
                    "title": title,
                    "category_id": category_id,
                    "start_date": start_date,
                    "reply_negotiation": reply_negotiation,
                    "id": id,
                    "status": status
                },
                success: function (data) {

                    notify('入札金額が更新されました！', 'alert_success');

                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                },
            });
        })

        function notify(mssg, classA) {
            var from = 'top';
            var align = 'right';
            var icon = "";
            var type = 'inverse';
            var animIn = 'animated fadeInRight';
            var animOut = 'animated fadeOutRight';
            $.growl({
                icon: icon,
                title: '',
                message: mssg,
                url: ''
            }, {
                element: 'body',
                type: type,
                allow_dismiss: true,
                placement: {
                    from: from,
                    align: align
                },
                offset: {
                    x: 30,
                    y: 30
                },
                spacing: 10,
                z_index: 999999,
                delay: 2500,
                timer: 2500,
                url_target: '_blank',
                mouse_over: false,
                animate: {
                    enter: animIn,
                    exit: animOut
                },
                icon_type: 'class',
                template: '<div data-growl="container" class="alert ' + classA + '" role="alert">' +
                    '<span data-growl="icon"></span>' +
                    '<span data-growl="title"></span>' +
                    '<span data-growl="message"></span>' +
                    '</div>'
            });
        };

        $('.btn_search').click(function () {
            let data_search = $(".search_list_box").val();

            var data = JSON.parse($('#data_box_num').attr('data-array'));

            console.log('search' + data);
            $.each(data, function (index, value) {

                $('#box').remove();

                if (value.name === data_search) {
                    let html = '';
                    html += "<tr id='box'>";
                    html += "<td>" + value.box + "</td>";
                    html += "<td>" + value.member_id + "</td>"
                    html += "<td>" + value.name + "</td>"
                    html += "<td><a  id='remove_box'  class='remove_box_" + value.box + "'  onClick='deleteBox(" + value.box + "," + value.box + value.member_id + ")'   ><i  class='feather" + " icon-trash-2" + " f-w-600" + " f-16 " + "text-c-red'></a></i></td>"
                    html += "</tr>";
                    $('#table_list_box').append(html);
                }
            });
        })

        $('.btn_reset').click(function () {

            var data = JSON.parse($('#data_box_num').attr('data-array'));
            let data_search = $(".search_list_box").val('');
            var html = '';

            $.each(data, function (index, value) {

                if (data_search !== value.name) {
                    html += "<tr id='box'>";
                    html += "<td>" + value.box + "</td>";
                    html += "<td>" + value.member_id + "</td>"
                    html += "<td>" + value.name + "</td>"
                    html += "<td><a  id='remove_box'  class='remove_box_" + value.box + "'  onClick='deleteBox(" + value.box + "," + value.box + value.member_id + ")'   ><i  class='feather" + " icon-trash-2" + " f-w-600" + " f-16 " + "text-c-red'></a></i></td>"
                    html += "</tr>";
                }
            });

            $('#table_list_box').html(html);

        })

        function deleteBoxExist (id){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                }
            });

            $.ajax({
                url: '{{ route('auction.delete_box_exist') }}',
                method: "GET",
                data: {
                    "id": id,
                },
                success: function (data) {

                    if(data !== '')
                    {
                    $('.box_'+data).remove();
                        
                    notify('入札金額が更新されました！', 'alert_success');
                    }

                    

                    // setTimeout(function () {
                    //     location.reload();
                    // }, 3000);
                },
            });
        }

    </script>
@endpush

