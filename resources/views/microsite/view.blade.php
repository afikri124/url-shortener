@extends('layouts.authentication.master')
@section('title', $data->title)
@section('script')
<script type="text/javascript">
    const getRandomNumber = (limit) => {
        return Math.floor(Math.random() * limit);
    };
    const getRandomColor = () => {
        const h = getRandomNumber(360);
        return `hsl(${h}deg, 100%, 90%)`;
    };

    const setBackgroundColor = () => {
        const randomColor = getRandomColor();
        const randomColor2 = getRandomColor();
        // document.body.style.background = randomColor;
        document.body.style.backgroundImage = "linear-gradient(to bottom right, "+ randomColor +", "+ randomColor2 +")";
    };

    setBackgroundColor()

    setInterval(() => {
        setBackgroundColor();
    }, 1500);
</script>
@endsection
@section('content')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <div class="card">
                <div class="card-body text-center">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mb-2">
                        <a href="{{asset($data->avatar)}}" class="app-brand-link gap-2">
                            <span class="app-brand-logo demo">
                                <img src="{{asset($data->avatar)}}" style="object-fit: cover;"
                                    height="100px">
                            </span>
                        </a>
                    </div>
                    <h4>{{$data->title}}</h4>
                    <span>{!!$data->bio!!}</span>
                    <br>
                    <div class="row p-2">
                        @foreach($links as $l)
                        <a href="{{$l->link}}" target="_blank"
                            class="btn btn-block rounded-pill btn-outline-danger my-2">{{$l->title}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
