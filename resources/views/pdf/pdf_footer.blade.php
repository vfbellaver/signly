<hr>
<!-- logo -->
<div class="col-xs-2">
    <img class="img_footer my_float"
         src="{{asset('storage/proposal_settings/'.Auth::user()->id.'/'.$footer->path_image)}}"
         alt="Your Logo" height="70">
</div>

<!-- info -->
<div class="col-xs-10 pull-right">

    <div class="col-xs-4">
        <label class="control-label">
            {{$footer->user_street.', '
            .$footer->user_city.' , '
            .$footer->user_state.'. '
            }}
        </label>
    </div>
    <br>

    <div class="col-xs-4">
     <label class="control-label">{{Auth::user()->email}}</label>
    </div>
    <br>

    <div class="col-xs-4">
        <label class="control-label">{{$footer->user_zipcode}}</label>
        <label class="control-label">{{$footer->user_phone}}</label>
        <label class="control-label">{{$footer->website}}</label>
    </div>
    <br>
</div>