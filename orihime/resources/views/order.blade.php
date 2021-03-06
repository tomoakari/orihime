<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- APIトークンの読み込み -->
    <script>
        window.Laravel = {!! json_encode([
            'apiToken' => \Auth::user()->api_token ?? null
        ]) !!};
    </script>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/order.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/haxanstyle.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">

        <!-- モーダルウィンドウ -->
        <div class="modal_wrap">
            <input id="trigger" type="checkbox">
	        <div class="modal_overlay">
	            <label for="trigger" class="modal_trigger"></label>
	            <div class="modal_content">
		            <label for="trigger" class="close_button">&#x2716;&#xfe0f;</label>
		            <h2>注文新規登録</h2>
                    <p>
                        契約先
                        <select v-model="newOrderData.newCustomer_code" v-on:blur="setDeliveryList">
                            <option v-for="option in customerList" v-bind:value="option.key">
                                @{{ option.value }}
                            </option>
                        </select>
                        <span class="errorMessage">@{{ errorMessage.company }}</span>
                    </p>
                    <p>
                        出荷先
                        <select v-model="newOrderData.newCompany_id" v-on:blur="getProductList">
                            <option v-for="option in deliveryList" v-bind:value="option.key">
                                @{{ option.value }}
                            </option>
                        </select>
                    </p>
                    <p>
                        出荷指図No.
                        <input type="text" v-model="newOrderData.newOpt_order_no">
                    </p>
                    <p>
                        品番
                        <select v-model="newOrderData.newProduct_code" v-on:blur="setMaterialList">
                            <option v-for="option in productCodeList" v-bind:value="option.key">
                                @{{ option.value }}
                            </option>
                        </select>
                        <span class="errorMessage">@{{ errorMessage.product }}</span>

                    </p>
                    <p>
                        生番
                        <select v-model="newOrderData.newMaterial_code" v-on:blur="setColorList">
                            <option v-for="option in materialList" v-bind:value="option.key">
                                @{{ option.value }}
                            </option>
                        </select>
                    </p>
                    <p>
                        色番
                        <select v-model="newOrderData.newProduct_id" v-on:blur="setProductDetail">
                            <option v-for="option in colorList" v-bind:value="option.key">
                                @{{ option.value }}
                            </option>
                        </select>
                    </p>
                    <p>
                        納品日
                        <input type="date" v-model="newOrderData.newDelivery_date" v-on:blur="setExpShipDate">
                        （発送予定日：@{{ exp_ship_date }}）
                        <span class="errorMessage">@{{ errorMessage.delivery_date }}</span>
                    </p>
                    <p>
                        メートル数
                        <input type="number" v-model="newOrderData.newOrder_length" v-on:blur="setRoll">m
                        <span class="errorMessage">@{{ errorMessage.order_length }}</span>
                    </p>
                    <p>
                        反数
                        <input type="number" v-model="newOrderData.newRoll_amount">
                        （ 一反：@{{ roll_length }}m ）
                        <span class="errorMessage">@{{ errorMessage.roll_amount }}</span>
                    </p>
                    <p>
                        備考
                        <input type="text" v-model="newOrderData.newRemarks">
                    </p>
                    <p>
                        アラート
                        <input type="checkbox" name="check" v-model="newOrderData.newLacking_flg">
                    </p>
                    <p>
                        <input type="button" value="送信" v-on:click="sendNewOrder">
                    </p>
		        </div>
	        </div>
        </div>

        <!-- ヘッダー -->
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/home') }}">
                    注文管理
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto header_input_list">
                        <li>
                            <select v-model="search.customer_code" v-on:blur="setSearchDeliveryList">
                                <option v-for="option in searchCustomerList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
                            </select>
                        </li>
                        <li>
                            <select v-model="search.company_id">
                                <option v-for="option in searchDeliveryList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
                            </select>
                        </li>
                        <li class="nav-item">
                            <button class="" v-on:click="getSearchProductList">しぼりこむ</button>
                        </li>
                        <li>
                            <select v-model="search.product_code" v-on:blur="setSearchMaterialList">
                                <option v-for="option in searchProductList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
                        </select>
                        </li>
                        <li>
                            <select v-model="search.material_code" v-on:blur="setSearchColorList">
                                <option v-for="option in searchMaterialList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
                        </select>
                        </li>
                        <li>
                            <select v-model="search.product_id">
                                <option v-for="option in searchColorList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
                        </select>
                        </li>
                        <li>
                            <select v-model="search.delivery_date" >
                                <option v-for="option in searchDateList" v-bind:value="option.key">
                                    @{{ option.value }}
                                </option>
                        </select>
                        </li>

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        <li class="nav-item">
                            <button class="" v-on:click="searchOrders">表示</button>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">

        <div class="container order_table_container">
                <div class="row justify-content-center">
            <div>
                <table class="order_table">
                    <!-- ヘッダー行 -->
                    <thead>
                        <tr>
                            <td>品番</td>
                            <td>出荷先</td>
                            <td v-for="value in calenderInt" class="calendar_cell">
                                @{{ value }}
                            </td>
                        </tr>
                    </thead>

                    <!-- ２行目以降 -->
                    <tbody>
                    <tr v-for="order in orders">
                        <td>@{{order.product_code}}</td>
                        <td>@{{order.delivery_name}}</td>
                        <template v-for='dayInt in calenderInt'>
                            <template v-for='element in order.delivery_date'>
                                <template v-if="element.day == dayInt">
                                    <template v-if="element.order_id">
                                        <td v-on:click="showOrder(element.order_id)"
                                            :class="lackingColor(element.lacking_flg)" >
                                            @{{element.order_length}}
                                        </td>
                                    </template>
                                    <template v-else>
                                        <td >
                                        </td>
                                    </template>
                                </template>
                            </template>
                        </template>
                    </tr>
                    </tbody>
                </table>
            </div>
            </div>
            </div>


            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <div class="card cardmargin">
                    <div class="card-header">発注内容</div>
                    <div class="card-body">
                    <p>
                        契約先
                        @{{ detail.customer_name}}
                    </p>
                    <p>
                        出荷先
                        @{{ detail.delivery_name }}
                    </p>
                    <p>
                        出荷指図No.
                        @{{ detail.opt_order_no }}
                    </p>
                    <p>
                        品番
                        @{{ detail.product_code }}
                    </p>
                    <p>
                        生番
                        @{{ detail.material_code }}
                    </p>
                    <p>
                        色番
                        @{{ detail.color_code }}
                    </p>
                    <p>
                        納品日
                        <input type="date" v-model="detail.delivery_date" v-on:blur="updSetExpShipDate">
                        （発送予定日：@{{ detail.view_exp_ship_date }}）
                        <span class="errorMessage">@{{ updErrMessage.delivery_date }}</span>
                    </p>
                        発送日
                        <input type="date" v-model="detail.ship_date">
                    </p>
                    <p>
                        オーダーメートル数
                        <input type="number" v-model="detail.order_length" v-on:blur="updSetRoll">m
                        <span class="errorMessage">@{{ updErrMessage.order_length }}</span>
                    </p>
                    <p>
                        結果メートル数
                        <input type="number" v-model="detail.result_length">m
                        <span class="errorMessage">@{{ updErrMessage.result_length }}</span>
                    </p>
                    <p>
                        反数
                        <input type="number" v-model="detail.roll_amount">
                        （ 一反：@{{ detail.roll_length }}m ）
                        <span class="errorMessage">@{{ updErrMessage.roll_amount }}</span>
                    </p>
                    <p>
                        備考
                        <input type="text" v-model="detail.remarks">
                    </p>
                    <p>
                        アラート
                        <input type="checkbox" name="check" v-model="detail.lacking_flg">
                    </p>
                    <p>
                        <input type="button" value="更新" v-on:click="sendUpdateOrder">
                        <input type="button" value="削除" v-on:click="sendDeleteOrder">
                        <span class="errorMessage">@{{ updErrMessage.order_id }}</span>
                    </p>
                    </div>
                </div>
            </div>

        </main>



        <div class="flex">
            <div class="rigthBox">
                <label for="trigger" class="open_button">
                <a class="fab" >
                    <i class="fas fa-plus"></i>
                </a>
                </label>
            </div>
        </div>


    </div>
</body>
</html>
