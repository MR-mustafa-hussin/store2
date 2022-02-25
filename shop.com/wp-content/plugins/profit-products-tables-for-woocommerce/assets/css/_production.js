.woot-data-table{
    position: relative;
}
.woot-data-table .table23-wrapper{
    width: 100%;
    padding: 9px 0;
    background: #fff;
    overflow-x: auto;
    overflow-y: hidden;
    position: relative;
}

.woot-data-table table tbody td{
    vertical-align: middle !important;
}

td .woot-data-table .table23-wrapper{
    padding: 0;
}

.table23-wrapper td table{
    margin: 0;
}

.woot-data-table table{
    width: 100%;
    color: #373a3c;
    border-collapse: separate;
    border: 1px solid #eceeef;
    border-spacing: 2px;
    line-height: 23px;
    text-align: left;
}

.woot-data-table table thead th{
    border-bottom: 2px solid #eceeef;
}

.woot-data-table table tfoot th{
    border-top: 2px solid #eceeef;
}

.woot-data-table table thead th,
.woot-data-table table tfoot th{
    font-size: 0.95em;
    position: relative;
    border-right: 1px solid #eceeef;
    padding: 7px 7px;
    color: #333;
    white-space: nowrap;
    text-transform: none !important;
    font-size: 1.1em;
}

.woot-data-table table th.order-able{
    cursor: pointer;
}

.woot-data-table table th.order-able:before{
    font-family: icofont;
    font-style: normal;
    font-weight: normal;
    color: #ccc;
    position: absolute;
    right: 0.8em;
    top: -0.2em;
    content: '↑';
    font-size: 0.7em;
}

.woot-data-table table th.order-able:after{
    font-family: icofont;
    font-style: normal;
    font-weight: normal;
    color: #ccc;
    position: absolute;
    right: 0.1em;
    top: -0.2em;
    content: '↓';
    font-size: 0.7em;
}

.woot-data-table table th.order-able.order-asc:before,
.woot-data-table table th.order-able.order-desc:after
{
    color: #777;
}

.woot-data-table table tbody > tr,
.woot-data-table table tfoot > tr{
    background-color: #fff;
}

.woot-data-table table tbody tr:nth-of-type(odd){
    background-color: #f7f7f7;
}


.woot-data-table table tbody tr:nth-of-type(even){
    background-color: #fefefe;
}


.woot-data-table table tbody tr:hover{
    background-color: #f7f9fe;
}

.woot-data-table table tbody td{
    position: relative;
    border-bottom-width: 0;
    border-right: 0;
    white-space: initial;
    padding: 7px 7px;
    font-size: 0.95em;
}

.woot-data-table table tbody tr:first-child td, .woot-data-table th{
    border-top: none;
    position: relative;
}

/**************************/

.woot-data-table .table23-pagination{
    float: right;
    text-align: right;
    margin-top: 7px;
    list-style: none;
    clear: both;
    margin-bottom: 4px;
}

.woot-data-table .table23-pagination > span{
    display: inline-block;
    width: auto;
}

.woot-data-table-current-page{
    display: block !important;
    width: auto !important;
    white-space: nowrap;
    font-style: italic;
    font-size: 0.75em;
    text-align: center;
    background: #fff !important;
}

.woot-data-table-th{
    position: absolute;
    top: 0;
    right: 0;
}

.table23-tr-notice{
    position: absolute;
    width: 100%;
    z-index: 999 !important;
    opacity: 0.85;
    color: orangered;
    font-size: 2em;
    background: repeating-linear-gradient(
        135deg,
        #f7f9fe,
        #f7f9fe 10px,
        #92bce0 10px,
        #92bce0 20px
        ) !important;
    font-weight: bold;
    text-align: center;
}

.table23-load-more-button{
    display: block;
    text-align: center;    
}

.table23-load-more-button a{
    color: orange;
}

.table23-hidden{
    display: none;
}

.woot-data-table .dashicons, .woot-data-table .dashicons-before:before{
    line-height: 1.4 !important;
}

.woot-editable-textarea{
    width: 100%;
    height: 60px;
    min-width: 220px;
}

.woot-data-table-pager{
    border: 1px solid #ccc;
    border-radius: 3px;
    font-family: inherit;
    padding: 3px;
    height: auto !important;
    min-width: 40px !important;
    margin: 0 5px;
}

.table23-per-page-selector{
    margin: 12px 0 3px 0;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-family: inherit;
    padding: 3px;
    width: auto !important;
    height: auto !important;
}

td[data-field-type="textinput"]:hover{
    cursor: pointer;
}

div.woot_td_cell{
    padding: 4px 0;
}


.table23-flow-header{
    position: absolute;
    top: 0;
    background: #fff;
    z-index: 999;
    opacity: 0.9;
}

.table23-flow-header > div{
    float: left;
    padding-left: 1px;
    color: #588fe5;
    color: green;
}

.woot-in-compact-mode .table23-flow-header{
    display: none !important;
}


/* Create a custom radio button */
.woot-checkmark {
    position: absolute;
    top: -1px;
    left: 0;
    height: 18px;
    width: 18px;
    background-color: #ccc;
    border-radius: 10%;
    border: solid 1px #fff;
    font-size: 15px;
    line-height: 18px;
    text-align: center;
    color: #fff;
    font-weight: normal !important;
}

.woot-checkmark-container{
    position: relative;
}

.woot-checkmark-container input{
    height: 0 !important;
}


/* On mouse-over, add a grey background color */
.woot-checkmark-container:hover input ~ .woot-checkmark {
    /*background-color: #946497;*/
}

.woot-checkmark-container:hover input ~ .woot-checkmark::before {
    /*background-color: #946497;*/
}

/* When the radio button is checked, add a blue background */
.woot-checkmark-container input:checked ~ .woot-checkmark {
    /*background-color: #2196F3;*/
    background-color: #674399;
}
.woot-checkmark-container input:checked ~ .woot-checkmark::before {
    content: '✔'
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.woot-checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

/* Show the indicator (dot/circle) when checked */
.woot-checkmark-container input:checked ~ .woot-checkmark:after {
    display: block;
}

/* Style the indicator (dot/circle) */
.woot-checkmark-container .woot-checkmark:after {
    top: 9px;
    left: 9px;
    width: 8px;
    height: 8px;
    border-radius: 70%;
}

/******************************* cart start *****************************************/
.woot-woocommerce-cart{
    position: relative;
    padding: 7px;
    width: auto;
    height: auto;
    background: #f7f9fe;
    border: solid 1px #92bce0;
    border-radius: 9px;
    float: right;
}

.woot-clearfix{
    clear: both;
}

.woot-add-to-cart-all-btn{
    position: absolute;
    top: -12px;
    left: 0;
    display: none;
    align-items: center;
    justify-content: center;
    background-color: #37C;
    color: #fff !important;
    text-decoration: none !important;
    height: auto;
    padding: 9px 4px;
    margin: 0;
    box-sizing: border-box;
    border-radius: 3px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.4);
    transition: background-color 190ms linear;
    border: 1px solid rgba(0,0,0,0.2);
    cursor: pointer;
    font-size: 0.8em !important;
    line-height: 0 !important;
}

.woot-add-to-cart-all-btn:hover{
    background: #2f477a;
}

.woot_td_quantity input{
    width: 55px !important;
}

th[data-key="add_to_cart"] .woot-checkmark-container{
    position: absolute;
    right: 6px;
    top: 12px;
}

/******************************* cart end *****************************************/
/******************************* attachments start *******************************/

#attachments_in_popup .woot_td_title{
    min-width: 150px;
    font-size: 0.95em;
    color: #428bca !important;
}

#attachments_in_popup .woot_td_description{
    font-style: italic;
    width: 40%;
}

#attachments_in_popup tbody td{
    vertical-align: middle;
}

/******************************* attachments end *******************************/

.woot-data-table a{
    text-decoration: none;
    color: #428bca;
}

.woot-data-table a:hover{
    text-decoration: none;
    color: #92bce0;
}

.woot-data-table table thead th{
    /*text-transform: uppercase;*/
}

.woot-data-table table{
    font-size: 14px !important;
}

.woot-modal .woot-data-table table,
.woot-modal .woot-data-table table thead th{
    font-size: 15px;
}

.woot_td_comment_content{
    max-width: 400px;
}

.woot_td_stars,
.woot_td_comment_date
{
    vertical-align: middle;
}

#woot_img_preview{
    position: absolute;
    border: 1px solid #ccc;
    background: #333;
    padding: 5px 5px 11px 5px;
    display:none;
    color: #fff;
    z-index: 20001;
}


span.woot-html-check {
    font-size: 18px;
    line-height: 0;
    font-family: monospace;
    color: green;
    font-size: 27px;
}

span.woot-html-cross {
    font-size: 12px;
    line-height: 0;
    font-family: monospace;
}

.woot_td_actions span.woot-html-cross{
    width: 5px;
    height: 5px;
}

.woot_td_actions span.woot-html-cross > span{
    left: 5px;
    top: 5px;
}

/********************************************************/

@media print {
    .woot-text-search,
    .woot-filter-list,
    .table23-per-page-selector,
    .table23-pagination,
    .woot-woocommerce-cart,
    .woot_td_ask_me,
    .woot_td_add_to_cart,
    th[data-key="ask_me"],
    th[data-key="add_to_cart"],
    .woot-data-table table th.order-able.order-asc:before,
    .woot-data-table table th.order-able.order-desc:after,
    .woot-data-table table th.order-able:after,
    .woot-data-table table th.order-able:before
    {
        display: none;
    }
}

.woot-btn{
    align-items: center;
    justify-content: center;
    background-color: #37C;
    color: #fff !important;
    text-decoration: none !important;
    height: 33px;
    padding: 1px 1.1rem;
    margin: 0 5px;
    box-sizing: border-box;
    border-radius: 3px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.4);
    transition: background-color 190ms linear;
    border: 1px solid rgba(0,0,0,0.2);
    cursor: pointer;
    font-size: 14px !important;
    line-height: 31px !important;
}

.woot-btn-1{
    height: auto;
    margin: 0;
    padding: 15px;
    font-size: 23px !important;
}

.woot-btn-icon{
    height: auto;
    padding: 0 0.7rem;
    line-height: 0 !important;
    font-size: 20px !important;
}

.woot-btn:hover{
    background-color: #428bca;
}


/**************************************************************/

.woot_td_stock_quantity,
.woot-modal .woot_td_thumbnail,
.woot_td_single{
    width: 1px;
}

.woot-modal .woot_td_post_title,
.woot-modal .woot_td_title{
    width: 40%;
    font-size: 1.05em;
    vertical-align: baseline;
}


/**************************************************************/

.woot_td_id{
    width: 1px;
    font-weight: bold;
    vertical-align: top;
    line-height: 35px;
    vertical-align: top;
}

div.woot_td_id{
    width: 100%;
}

.woot_td_post_title{
    vertical-align: top;
    line-height: 35px;
}

.woot_td_post_title a{
    /*font-style: italic;*/
    color: #428bca !important;
    font-size: 1.05em !important;
    text-decoration: none !important;
}

.woot_td_thumbnail{
    width: 1px;
    text-align: center;
}

.woot_td_sku,
.woot_td_add_to_cart,
.woot_td_ask_me,
.woot_td_variations,
.woot_td_add_to_cart{
    white-space: nowrap !important;
}

.woot_td_add_to_cart{
    width: 1px;
}

.woot-data-table table thead th[data-key="add_to_cart"],
.woot-data-table table tfoot th[data-key="add_to_cart"]{
    text-transform: uppercase !important;
    font-size: 1em;
}

.woot_td_add_to_cart input[type="number"]{
    border: 1px solid #ccc;
    border-radius: 3px;
    font-family: inherit;
    padding: 6px;
    width: 77px;
    max-width: 77px;
}

.woot-btn.woot-add-to-cart-btn{
    position: relative;
    display: inline-block;
    margin: 5px 0 0 0;
    background-color: #a46497;
    font-weight: bold;
    width: fit-content;
    white-space: nowrap;
}

.woot_td_add_to_cart .woot-add-to-cart-btn{
    display: block;    
}

.woot-add-to-cart-input{
    margin-right: 4px;
}

.woot_td_add_to_cart .woot-btn:hover{
    background-color: #96588a;
}

.woot_td_add_to_cart .woot-checkmark-container{
    margin-left: 5px;
}

.woot_td_price{
    font-size: 1.0em !important;
    color: orange;
}

.woot_td_regular_price{
    font-size: 1.0em !important;
    color: orange;
}

.woot_td_sale_price{
    font-size: 1.0em !important;
    color: orange;
}


/********************** customization example ****************************************/

*[class*='woot-pa_color-green']{
    color: green !important;
}

*[class*='woot-pa_color-red']{
    color: red !important;
}

.ranger23-bar{
    background: #96588a !important;
    border-color: #96588a !important;
}



.dashicons-before.dashicons-no{
    color: red;
}

.dashicons-before.dashicons-no:before{
    font-size: 2em;
    line-height: 0.9 !important;
}

.woot-modal .woocommerce-Price-amount.amount{
    color: orange;
}

.table23-load-more-button a{
    font-size: 1.1em !important;
}

.woot_woocommerce_tables .table23-display-cell-info{
    display: none;
    color: orange;
    position: fixed;
    right: 3%;
    top: 25%;
    z-index: 999;
    background: #f7f9fe;
    padding: 7px;
    border: solid 1px #92bce0;
    border-radius: 4px;
}

.woot_td_average_rating{
    vertical-align: bottom;
}

.woot_td_average_rating .woocommerce > span{
    font-weight: bold;
    margin-left: 18px;
}

th[data-key="add_to_cart"],
span[data-key="add_to_cart"]{
    color: red !important;
    border-bottom: 1px dotted !important;
    cursor: pointer;
    animation-duration: 1s;
    animation-delay: 0;/*300ms*/
    animation-iteration-count: infinite;
    /*animation-name: bounce;*/
}

b[data-key="add_to_cart"]{
    display: inline-block;
}

.woot-tax-term{
    display: inline-block;
    padding: 1px 4px 1px 4px;
    background: #e7f3f7;
    border-radius: 2px;
    border: solid 1px #0090cb !important;
    line-height: initial;
    font-size: 0.8em;
}

.woot-content-in-popup .horizontal-scrollbar23-wrapper{
    position: fixed !important;
}

.woot-slider-label{
    font-weight: bold;
    margin-bottom: 7px;
    font-size: 12px;
}

.woot_single_product .woot_td_value{
    width: 85%;
}

.woot_single_product .woot_td_title{
    width: 15%;
}

.woot_single_product .woot_td_value p{
    margin-bottom: 9px;
}

.woot_single_product .woot-checkmark-container{
    display: none;
}

.woot_td_sku{
    font-family: monospace;
    font-weight: bold;
}

.woot-reviews-shortcode .woocommerce a{
    display: none;
}

/***** animation *****/
@-webkit-keyframes woot-bounce{20%,53%,80%,from,to{-webkit-animation-timing-function:cubic-bezier(.215,.61,.355,1);animation-timing-function:cubic-bezier(.215,.61,.355,1);-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0)}40%,43%{-webkit-animation-timing-function:cubic-bezier(.755,.050,.855,.060);animation-timing-function:cubic-bezier(.755,.050,.855,.060);-webkit-transform:translate3d(0,-30px,0);transform:translate3d(0,-30px,0)}70%{-webkit-animation-timing-function:cubic-bezier(.755,.050,.855,.060);animation-timing-function:cubic-bezier(.755,.050,.855,.060);-webkit-transform:translate3d(0,-15px,0);transform:translate3d(0,-15px,0)}90%{-webkit-transform:translate3d(0,-4px,0);transform:translate3d(0,-4px,0)}}@keyframes woot-bounce{20%,53%,80%,from,to{-webkit-animation-timing-function:cubic-bezier(.215,.61,.355,1);animation-timing-function:cubic-bezier(.215,.61,.355,1);-webkit-transform:translate3d(0,0,0);transform:translate3d(0,0,0)}40%,43%{-webkit-animation-timing-function:cubic-bezier(.755,.050,.855,.060);animation-timing-function:cubic-bezier(.755,.050,.855,.060);-webkit-transform:translate3d(0,-30px,0);transform:translate3d(0,-30px,0)}70%{-webkit-animation-timing-function:cubic-bezier(.755,.050,.855,.060);animation-timing-function:cubic-bezier(.755,.050,.855,.060);-webkit-transform:translate3d(0,-15px,0);transform:translate3d(0,-15px,0)}90%{-webkit-transform:translate3d(0,-4px,0);transform:translate3d(0,-4px,0)}}.woot-bounce{-webkit-animation-name:woot-bounce;animation-name:woot-bounce;-webkit-transform-origin:center bottom;transform-origin:center bottom}

.woot-hidden{
    height: 0;
    overflow: hidden;
}

.woot-filter-show-btn{
    position: relative;
    padding-right: 25px;
    background: #663399;
}

.woot-filter-show-btn span.dashicons-before{
    position: absolute;
    right: 0;
    top: -5px;
}


.woot-tab-content{
    margin-top: 15px;
}

.woot-tab-content-hidden{
    height: 0;
    overflow: hidden;
}

.woot-tab-content-hide .horizontal-scrollbar23-wrapper{
    display: none !important;
}

.woot-tab-content-current{
    height: auto !important;
    overflow: visible;
}

.woot-tab-link-current{
    background: orange;
}

.woot-tab-link-current:hover{
    background-color: coral;
}

.woot_td_average_rating .woocommerce .star-rating{
    font-size: 1.2em;
    color: burlywood;
}

.woot-order-select-zone{
    float: left;
}

.woot_td_file{
    background: #f1f3f4;
}

.woot-more-less-container{
    width: 100%;
    max-height: 60px;
    opacity: 0.95;
    overflow: hidden;
    cursor: pointer;
    border-bottom: dotted 1px red;
    transition: all 0.07s ease;
}

.woot-more-less-container > div > strong{
    display: none;
    font-size: 120%;
    margin-bottom: 11px;
}

.woot-more-less-container.woot-more-less-container-active{
    position: fixed;
    z-index: 9999;
    width: 100vh;
    left: 25%;
    top: 25%;
    max-height: fit-content;
    overflow: auto;
    cursor: auto;
}

.woot-more-less-container.woot-more-less-container-active > div{
    position: relative;
    max-height: 300px;
    overflow: auto;
    background: #fff;
    padding: 7px 20px 7px 9px;
}

.woot_single_product .woot-more-less-container.woot-more-less-container-active{
    position: initial;
    z-index: 1;
    width: 100%;
    left: initial;
    top: initial;
    max-height: fit-content;
    overflow: auto;
    cursor: auto;
}

.woot_single_product .woot-more-less-container > div > strong{
    position: absolute;
    top: -100px;
}

.woot_single_product .woot-reviews-shortcode .woot-more-less-container > div > strong{
    position: inherit;
    top: 0;
}

.woot-more-less-container.woot-more-less-container-active > div > strong{
    display: block;
}

.woot-more-less-container-closer{
    display: none;
    position: absolute;
    right: 5px;
    top: 5px;
    color: red !important;
    font-size: 150%;
    font-weight: bold;
}

.woot-more-less-container-closer:hover{
    text-decoration: none;
}

.woot-more-less-container-active .woot-more-less-container-closer{
    display: block;
}

.woot-num-cell{
    display: block;
    margin: 0 auto;
    background: #e9e9e9;
    color: #428bca;
    width: fit-content;
    padding: 4px 20px;
    border: solid 2px #ddd;
    font-weight: bold;
    text-align: center;
    height: fit-content;
    border-radius: 23px;
    min-width: 23px;
    min-height: 23px;
    line-height: 23px;
}

.woot-notice{
    color: #404040;
    border: solid 1px #0090cb !important;
    background: #e7f3f7;
    padding: 9px;
    font-size: 0.80em;
    margin-bottom: 9px;
}

/*******************************************************/

.woot_td_post_title .woot-profuct-tag{
    font-size: 9px;
}

.woot-profuct-tag-container{
    position: absolute;
    top: -10px;
    right: 0;
}

.woot-profuct-tag {
    font-family: sans-serif;
    display: inline-block;
    color: #fff;
    position: relative;   
    padding: 4px 4px 5px 4px;
    border-top-left-radius: 4px;
    border-bottom-left-radius: 4px;
    margin: 0 9px 0 0;
    text-decoration: none;    
    line-height: 0;
}

.woot-profuct-tag:before {
    background: #fff;
    width: 7px;
    height: 7px;
    content: "";
    display: inline-block;
    border-radius: 20px;
    margin: 0 5px 0 0;
    line-height: 0;
}

.woot-profuct-tag:after {
    display: inline-block;
    border: 8px solid;
    height: 0;
    width: 0;
    position: absolute;
    right: -16px;
    top: 0;
    content: "";
    display: inline-block;
    border-bottom-left-radius: 1px;
    line-height: 0;
}

.woot-profuct-tag-featured{
    background: #588fe5;
}

.woot-profuct-tag-featured:after{
    border-color: transparent transparent transparent #588fe5;
}


.woot-profuct-tag-sale{
    background: orange;
}

.woot-profuct-tag-sale:after{
    border-color: transparent transparent transparent orange;
}

.woot-profuct-tag-out{
    background: red;
}

.woot-profuct-tag-out:after{
    border-color: transparent transparent transparent red;
}

/*******************************************************/

.woot-filter-cell-type-select .selectm23-selected{
    padding: 3px 18px 4px 5px;
}

.woot-filter-cell-type-select .selectm23-selected{
    cursor: pointer;
}

.woot-blank-page{
    background: #fff;
}

.woot_count_in_cart{
    z-index: 0;
    position: absolute;
    top: -4px;
    right: -4px;
    background: #a46497;
    line-height: 15px;
    display: block;
    border-radius: 20px;
    width: 15px;
    height: 15px;
    vertical-align: middle;
    text-align: center;
    font-size: 11px;
    border: solid 1px #fff;
}

.woot_td_file{
    position: relative;
}

.woot_td_file div a{
    position: absolute;
    top: 0;
    right: 0;
}

.woot-place-loader{
    width: 100%;
    z-index: 999 !important;
    opacity: 0.85;
    color: orangered;
    font-size: 2em;
    background: repeating-linear-gradient(
        135deg,
        #f7f9fe,
        #f7f9fe 10px,
        #92bce0 10px,
        #92bce0 20px
        ) !important;
    font-weight: bold;
    text-align: center;
}

.woot-thumbnail{
    max-width: initial !important;
}/* WOOT Modal Popup */
.woot-modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    min-height: 360px;
    background: black;
    opacity: .7;
    z-index: 15000;
}

.woot-modal {
    position: fixed;
    top: 50px;
    left: 10%;
    right: 10%;
    z-index: 15001;
    background: #fff;
    min-height: 240px;
    min-width: 320px;
    overflow: auto;
    bottom: 50px;
    animation: woot_modal_pop 0.45s 1 cubic-bezier(0.175, 0.885, 0.320, 1.275);
}

@-webkit-keyframes woot_modal_pop {
    0%   { -webkit-transform:scale(0.8); opacity:0; }
    100% { -webkit-transform:scale(1); opacity:1;  }
}
@-moz-keyframes woot_modal_pop {
    0%   { -moz-transform:scale(0.8); opacity:0; }
    100% { -moz-transform:scale(1); opacity:1;  }
}
@-o-keyframes woot_modal_pop {
    0%   { -o-transform:scale(0.8); opacity:0; }
    100% { -o-transform:scale(1); opacity:1;  }
}
@keyframes woot_modal_pop {
    0%   { transform:scale(0.8); opacity:0; }
    100% { transform:scale(1); opacity:1;  }
}

.woot-modal-inner-header {
    height: 50px;
    border-bottom: 1px solid #dfdfdf;
    box-shadow: 0 4px 4px -4px rgba(0, 0, 0, 0.1);
    position: relative;
}

.woot-modal-inner-footer {
    height: 50px;
    border-top: 1px solid #dfdfdf;
    box-shadow: inset 0 4px 4px -4px rgba(0, 0, 0, 0.1);
    position: absolute;
    bottom: 0;
    background: #FCFCFC;
    width: 100%;
    overflow: hidden;
    clear: both;
}

.woot-modal .woot-modal-inner-footer .button-large {
    padding: 0 35px 2px;
}

.woot-modal .woot-modal-inner-footer .woot-modal-button-large-1 {
    float: right;
    margin: 9px 11px 0 0;
    font-size: 0.95em;
}

.woot-modal .woot-modal-inner-footer .button-large-2 {
    float: left;
    margin: 9px 0 0 11px;
}

.woot-modal-inner-content {
    position: absolute;
    overflow: auto !important;
    padding: 0;
    top: 52px;
    left: 0;
    right: 0;
    bottom: 50px;
}

.woot-modal-close {
    position: absolute;
    top: 17px;
    right: 14px;
    height: 24px;
    width: 24px;
    overflow: hidden;
    outline: none;

    -webkit-transition-duration: 0s;
    transition-duration: 0s;
}

.woot-modal-close:focus {
    outline: none;
    box-shadow: none;
}

.woot-modal-close::before {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    font-size: 22px;
    color: #ccc;
    text-align: center;
    display: inline-block;
    vertical-align: middle;
    content: "x";

    -webkit-transition: color .25s ease;
    transition: color .25s ease;
}

.woot-modal-close:hover::before { color: #cb4a1f; }

.woot-modal-title {
    position: absolute;
    top: 0;
    margin: 0;
    line-height: 53px;
    left: 14px;
    color: #555;
    padding: 0;
    font-size: 21px;
    font-weight: bold;
}

.woot-modal-title span{
    color: red;
}

.woot-modal-title-info {
    position: absolute;
    top: 26px;
    right: 44px;
    margin: 0;
    line-height: 0;
    color: #999;
    padding: 0;
    font-size: 15px;
}

.woot-form-element-container {
    padding: 8px;
    position: relative;
}

.woot-form-element-container::after {
    content: '.';
    display: block;
    overflow: hidden;
    visibility: hidden;
    width: 0;
    height: 0;
    clear: both;
}

.woot-form-element-container {
    padding: 18px 20px;
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    border-bottom: 1px solid #e1e1e1;
    position: relative;
    background-color: #fff;
    clear: both;
}

.woot-form-element-container > p { color: #777; }

.woot-form-element-container:hover {
    background-color: #fcfcfc;
}

.woot-iframe-in-popup{
    border: 0;
    position: relative;
    width: 100%;
}

.woot-content-in-popup{
    color: #333;
}
/* Growl
 * Copyright 2015 Kevin Sylvestre
 * 1.3.2
 */
#growls{z-index:50000;position:fixed}#growls.default{top:30px;right:10px}#growls.tl{top:30px;left:10px}#growls.tr{top:30px;right:10px}#growls.bl{bottom:30px;left:10px}#growls.br{bottom:30px;right:10px}#growls.tc{top:30px;right:10px;left:10px}#growls.bc{bottom:10px;right:10px;left:10px}#growls.bc .growl,#growls.tc .growl{margin-left:auto;margin-right:auto}.growl{opacity:.8;position:relative;border-radius:4px;-webkit-transition:all .4s ease-in-out;-moz-transition:all .4s ease-in-out;transition:all .4s ease-in-out}.growl.growl-incoming{opacity:0}.growl.growl-outgoing{opacity:0}.growl.growl-small{width:200px;padding:5px;margin:5px}.growl.growl-medium{width:250px;padding:10px;margin:10px}.growl.growl-large{width:300px;padding:15px;margin:15px}.growl.growl-default{color:#fff;background:#7f8c8d}.growl.growl-error{color:#fff;background:#c0392b}.growl.growl-notice{color:#fff;background:#2ecc71}.growl.growl-warning{color:#fff;background:#f39c12}.growl .growl-close{cursor:pointer;float:right;font-size:14px;line-height:18px;font-weight:400;font-family:helvetica,verdana,sans-serif}.growl .growl-title{font-size:18px;line-height:24px}.growl .growl-message{font-size:14px;line-height:18px}
.woot-gallery-cell{
    min-width: 135px;
    width: 100%;
}

.woot-gallery {
    height: auto;
    grid-template: repeat(6, 1fr)/repeat(6, 1fr);
    grid-gap: 0.5em;
    display: flex;
    align-items: flex-start;
    flex-wrap: wrap;
}

.woot-gallery > div {
    width: 30px;
    margin: 1%;
}


.woot-gallery div.woot-gallery-nav > a {
    opacity: 0;
    position: absolute;
    color: #000;
    background-color: #000;
    font: bold 4em "Helvetica";
    text-shadow: 0 -1px 5px #fff, -1px 0px 5px #fff, 0 1px 5px #fff, 1px 0px 5px #fff;
    padding: 2rem;
    mix-blend-mode: difference;
    width: 100%;
    height: 100%;
    transition: all ease 1s;
}
.woot-gallery div.woot-gallery-nav > img {
    width: 100%;
    min-height: 100%;
    transition: all ease 1s;
}
.woot-gallery div.woot-gallery-nav:hover img {
    filter: blur(4px);
}
.woot-gallery div.woot-gallery-nav:hover a {
    opacity: 1;
}
.woot-gallery div.woot-gallery-nav {
    overflow: hidden;
    position: relative;
    box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.2), 0 3px 20px 0 rgba(0, 0, 0, 0.19);
}
.woot-gallery div.woot-gallery-nav,
.woot-gallery a {
    display: flex;
    justify-content: center;
    align-items: center;
    text-decoration: none;
}

[id^="woot-gallery-lightbox-"] {
    z-index: 20001;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    opacity: 0;
    transition: opacity 450ms ease-in-out;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}
[id^="woot-gallery-lightbox-"]:target {
    opacity: 1;
    pointer-events: inherit;
}
[id^="woot-gallery-lightbox-"]:target img {
    filter: blur(0);
}
[id^="woot-gallery-lightbox-"] .woot-gallery-content {
    top: 40px;
    max-width: 90%;
    position: relative;
    color: #fff;
    overflow: hidden;
}
[id^="woot-gallery-lightbox-"] .woot-gallery-content:hover > a.woot-gallery-close {
    /*
    opacity: 1;
    transform: scale(1, 1);
    */
}
[id^="woot-gallery-lightbox-"] .woot-gallery-content:hover > .woot-gallery-title {
    /*
    opacity: 1;
    transform: translateY(-3px);
    */
}
[id^="woot-gallery-lightbox-"] .woot-gallery-content:hover > .woot-gallery-title::after {
    /*opacity: 1;*/
}
[id^="woot-gallery-lightbox-"] .woot-gallery-content > * {
    transition: all 300ms cubic-bezier(.17,.67,.83,.67);
}
[id^="woot-gallery-lightbox-"] .woot-gallery-title {
    display: block;
    margin: 0;
    padding: 1em;
    position: relative;
    bottom: 68px;
    width: 100%;
    /*transform: translateY(50%);*/
    transform: translateY(-3px);
    font-size: 1.5em;
    /*opacity: 0;*/
    opacity: 1;
}
[id^="woot-gallery-lightbox-"] .woot-gallery-title::after {
    content: ' ';
    background-color: rgba(0, 0, 0, 0.4);
    bottom: 0;
    left: 0;
    height: 100%;
    width: 100%;
    position: absolute;
    transition: all 350ms ease-in-out 250ms;
    /*opacity: 0;*/
    opacity: 1;
    transform-origin: bottom;
    mix-blend-mode: soft-light;
}
[id^="woot-gallery-lightbox-"] img {
    max-height: 90vh;
    max-width: 100%;
    margin: 0;
    padding: 0;
    filter: blur(50px);
}
[id^="woot-gallery-lightbox-"] a.woot-gallery-close {
    width: 2em;
    height: 2em;
    position: absolute;
    right: 0;
    top: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    transform: scale(1, 1);
    opacity: 1;
    /*
    opacity: 0;
    transform: scale(0, 0);
    */
    transform-origin: right top;
    text-decoration: none;
    color: #fff;
}
[id^="woot-gallery-lightbox-"] a.woot-gallery-close::after {
    content: "X";
}

.woot-filter-list{
    clear: both;
    margin-bottom: 9px;
}

.woot-filter-list > div{
    float: left;
    margin-bottom: 7px;
}

.woot-text-search{
    display: inline-block;
    width: 100%;
    margin-bottom: 7px;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-family: inherit;
    padding: 6px;
    padding: 0.428571429rem;
    color: #333;
    font-size: 0.9em;
}

.woot-woocommerce-text-search-container{
    position: relative;
    width: 100%;
}

.woot-woocommerce-text-search-container input{
    width: 100%;
}

.woot-woocommerce-text-search-wrapper{
    position: absolute;
    height: auto;
    width: 100%;
    z-index: 9999;
    background: #fff;
}

.woot-woocommerce-text-search-wrapper .woot-text-search{
    display: none;
}

.woot-not-ready-text-search{
    border: dashed 2px red;
}

.woot-filter-reset{
    background-color: #663399;
    display: none;
}

.woot-filter-cell-sku{
    width: 100%;
    clear: both;
}

.woot-filter-cell-type-select,
.woot-filter-cell-type-calendar{
    min-width: 150px;
    margin-right: 5px;
}

.woot-filter-cell-type-select select{
    width: 100%;
    clear: both;
    box-shadow: 1px 1px 1px rgba(0,0,0,0.2);
    border: 1px solid #ccc;
    border-radius: 3px;
    font-family: inherit;
    padding: 6px;
    padding: 0.428571429rem;
    color: #333;
    font-size: 0.9em;
}

.woot-filter-cell-type-range_slider{
    width: 100%;
    margin-right: 5px;
    overflow: hidden;
}

.calendar23-selector{
    box-shadow: 1px 1px 1px rgba(0,0,0,0.2);
}

.calendar23-data-input{
    border-radius: 0 !important;
}

.woot-filter-navigation{
    margin-bottom: 9px;
}

.woot-filter-navigation-label{
    position: relative;
    display: inline-block;
    margin-right: 3px;
    padding: 1px 16px 1px 4px;
    background: #e7f3f7;
    border-radius: 2px;
    border: solid 1px #0090cb !important;
    line-height: initial;
    font-size: 0.8em;
    cursor: pointer;
    color: #663399;
}

.woot-filter-navigation-label:after{
    content: '✗';
    position: absolute;
    right: 1px;
    top: 0;
    color: #d11111;
}

/* https://codepen.io/andreasstorm/pen/ZorYLO */
.switcher23-toggle{position:relative;display:block;width:40px;height:20px;cursor:pointer;-webkit-tap-highlight-color:transparent;transform:translate3d(0,0,0)}
.switcher23-toggle:before{content:"";position:relative;top:3px;left:3px;width:34px;height:14px;display:block;background:#9a9999;border-radius:8px;transition:background .2s ease}
.switcher23-toggle span{position:absolute;top:0;left:0;width:20px;height:20px;display:block;background:#fff;border-radius:10px;box-shadow:0 3px 8px rgba(154,153,153,.5);transition:all .2s ease}
.switcher23-toggle span:before{content:"";position:absolute;display:block;margin:-18px;width:56px;height:56px;background:rgba(79,46,220,.5);border-radius:50%;transform:scale(0);opacity:1;pointer-events:none}
.switcher23{display:none!important}.switcher23:checked+.switcher23-toggle:before{background:lightblue}
.switcher23:checked+.switcher23-toggle span{background:cornflowerblue;transform:translateX(20px);transition:all .2s cubic-bezier(.8,.4,.3,1.25),background .15s ease;box-shadow:0 3px 8px rgba(79,46,220,.2)}
.switcher23:checked+.switcher23-toggle span:before{transform:scale(1);opacity:0;transition:all .4s ease}
.calendar23-month,
.calendar23-data-input{
    -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
    -khtml-user-select: none; /* Konqueror HTML */
    -moz-user-select: none; /* Old versions of Firefox */
    -ms-user-select: none; /* Internet Explorer/Edge */
    user-select: none; /* Non-prefixed version, currently
                          supported by Chrome, Opera and Firefox */
}

.calendar23-month{
    width: 300px;
    background: #fff;
    position: relative;
    height: auto;
}

.calendar23-month div {
    display: inline-block;
    width: 12.777%;
    float: left;
    text-align: center;
    margin-right: 1.5%;
    height: 40px;
    line-height: 40px;
    margin-bottom: 1.5%;
    background: #fff;
}

div.calendar23-label {
    display: block;
    width: 100%;
    margin-bottom: 20px;
    text-align: center;
    background: #4267B7;
    color: #fff;
    position: relative;
}

.calendar23-label span{
    cursor: pointer;
    display: inline-block;
    padding: 0 7px;
    animation: 7s linear infinite alternate calendar23-flash-border;
}

.calendar23-label span:hover{
    color: wheat !important;
}

@keyframes calendar23-flash-border {
    from { color: white;}
    to { color: #dafafd;}
}

.calendar23-prev{
    position: absolute;
    left: 3px;
    top: 0;
}

.calendar23-prev::after{
    color: #fff;
    content: '《';
}

.calendar23-next{
    position: absolute;
    right: 3px;
    top: 0;
}

.calendar23-next::after{
    color: #fff;
    content: '》';
}

.calendar23-prev,
.calendar23-next{
    color: #fff;
    text-decoration: none;
}

.calendar23-month .calendar23-big{
    width: 31.7864%;
    height: 50px;
    line-height: 50px;
}

.calendar23-dow {
    font-weight: bold;
    margin-bottom: 10px;
}

.calendar23-day {
    color: #333;
    cursor: pointer;
    box-shadow: inset 0 0 0 1px #eee;
}

.calendar23-day.calendar23-weekend { background: #fafaff; }
.calendar23-day.calendar23-today { background: #dafafd !important;}
.calendar23-day.calendar23-focused { background: #2EB67D !important; color: #fff;}

.calendar23-day:hover { background: #ECB22E !important; color: #fff;}

.calendar23-dummy-day {
    background: #f5f5f5 !important;
    color: #ccc;
}

/**************/

.calendar23-selector{
    position: relative;
}

.calendar23-calendar-wrapper{
    position: absolute;
    display: none;
    background: #fff;
    z-index: 99999;
}
:root {
    --ranger23-handler-width: 30px;
    --ranger23-handler-height: 20px;
}

.ranger23-container{
    position: relative;
    width: 100%;
    height: var(--ranger23-handler-height);
    /* outline: 1px solid #96588a; */
    background: thistle;
    overflow: hidden;
}

.ranger23-handler-left,
.ranger23-handler-right{
    position: absolute;
    z-index: 1;
    height: var(--ranger23-handler-height);
    width: var(--ranger23-handler-width);
    background: #c9356e;
    outline: 1px solid #fff;
    color: #fff;
}

.ranger23-handler-left{
    left: 0;
}

.ranger23-handler-right{
    right: 0;
}

.ranger23-handler-left:active, .ranger23-handler-right:active{
    cursor: e-resize
}

.ranger23-handler-left span,
.ranger23-handler-right span{
    display: inline-block;
    width: 100%;
    height: var(--ranger23-handler-height);
    padding-top: 35%;
    user-select: none;
    text-align: center;
    line-height: 0;
    font-size: 11px;
    overflow: hidden;
}

.ranger23-bar{
    position: absolute;
    height: var(--ranger23-handler-height);
    background: #96588a !important;
    width: calc(100% - 2*var(--ranger23-handler-width));
    left: var(--ranger23-handler-width);
}

.ranger23-bar-var-equal{
    background: repeating-linear-gradient(
        135deg,
        #f7f9fe,
        #f7f9fe 3px,
        #96588a 3px,
        #96588a 6px
        ) !important;
}

.ranger23-min,
.ranger23-max{
    position: absolute;
    z-index: 0;
    color: blueviolet;
    top: 50%;
    user-select: none;
    line-height: 0;
    font-size: 10px;
    cursor: pointer;
}

.ranger23-min{
    left: 2px;
}

.ranger23-max{
    right: 2px;
}

.selectm23{
    min-width: 150px;
    border: solid 1px #ddd;
    height: auto;
    min-height: 28px;
    padding: 0 2px;
    height: auto;
    position: relative;
    /*background-image: linear-gradient(#eee 1%, #fff 15%);*/
}

.selectm23-input{
    width: max-content !important;
    min-width: 10px !important;
    border: none !important;
    padding: 0 !important;
    line-height: 0 !important;
    min-height: available !important;
    background-color: transparent !important;
}

.selectm23-input:focus{
    outline: none !important;
    box-shadow: none !important;
}

.selectm23-selecton{
    position: relative;
}

.selectm23 .selectm23-options{
    width: 100%;
    position: absolute;
    max-height: 250px;
    overflow-y: auto;
    margin: 1px 0 0 -1px;
    background: #fff;
    z-index: 9999;
}

.selectm23-option{
    padding: 5px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    color: #333;
    font-size: 1em;
}

.selectm23-option-selected,
.selectm23-option:hover{
    background: #e7f3f7;
    color: #37C;
}

.selectm23-selected{
    font-size: 0.95em;
    display: inline-block;
    position: relative;
    margin: 3px 2px 3px 0;
    padding: 7px 18px 7px 5px;
    border: 1px solid #ddd;
    max-width: 100%;
    border-radius: 2px;
    background-color: #eeeeee;
    background-image: linear-gradient(#f4f4f4 20%, #f0f0f0 50%, #e8e8e8 52%, #eee 100%);
    background-size: 100% 99px;
    background-repeat: repeat-x;
    background-clip: padding-box;
    -webkit-box-shadow: 0 0 2px #fff inset, 0 1px 0 rgba(0, 0, 0, 0.05);
    box-shadow: 0 0 2px #fff inset, 0 1px 0 rgba(0, 0, 0, 0.05);
    color: #37C;
    line-height: 1em;
    cursor: move;
}

.selectm23-selected-cross{
    position: absolute;
    text-shadow: 1px 0;
    padding: 5px;
    color: #aaa;
    top: 4px;
    right: -2px;
    cursor: pointer;
    line-height: 0;
    font-size: 1.1em;
}


.selectm23-ghost{
    border: dashed 1px orange;
    background: green;
    color: #fff;
}

.selectm23-ghost .selectm23-selected-cross{
    display: none;
}

.selectm23-hidden{
    display: none;
}

.horizontal-scrollbar23-wrapper {
    bottom: 25px;
}

.horizontal-scrollbar23-track {
    width: 100%;
    position: relative !important;
    border: solid 1px #ccc;
    background: linear-gradient(to bottom, #ddd -50%, white 150%);
    cursor: pointer;
}

.horizontal-scrollbar23-handler{
    background: #92bce0;
    border: 1px solid #428bca;                
    position: absolute;
    top: -1px;
}

.horizontal-scrollbar23-track,
.horizontal-scrollbar23-handler{
    border-radius: 2px;
    height: 18px;
}

.horizontal-scrollbar23-handler:active{
    cursor: e-resize
}
