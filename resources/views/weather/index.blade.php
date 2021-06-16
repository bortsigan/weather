@extends('layouts.app')

@section('title', "Weather Weather Lang")

@section('content')

<div class="container">
    <div class="my-3 p-3 bg-white rounded shadow-sm">

        <form method="get" action="{{ route('index') }}">
            <div class="row">
                <div class="col-md-2">
                    <label class="sr-only" for="city">Enter City / Country</label>
                </div>
                <div class="col-md-8">
                    <div class="input-group mb-2 mr-sm-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Enter City / Country</div>
                        </div>

                        <input type="text" name="city" class="form-control" id="city" placeholder="Type your city or country here.." value="{{ $requestCity }}" />
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary mb-2">Submit</button>
                    <a href="{{ route('index', ['reset' => true, 'city' => null]) }}" class="btn btn-secondary mb-2"><i class="fa fa-undo" aria-hidden="true"></i></a>
                </div>
            </div>
        </form>

        <h4 class="border-bottom border-gray pb-2 mb-0">
            <i class="fa fa-sun-o" aria-hidden="true"></i>
            <i class="fa fa-cloud" aria-hidden="true"></i>
            <i class="fa fa-bolt" aria-hidden="true"></i>
             &nbsp; Current Temperature
        </h4>

        @if (!$temperatureResult['error'])
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <h3>
                                <img src="{{ $temperatureResult['img'] }}" />
                                {{ $temperatureResult['current_temp'] }} 
                                ({{ $temperatureResult['cloud'] }})
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>

            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-sm-12 text-center">
                           <h6>
                                @if (isset($temperatureResult['is_last_saved']))
                                    <span class="text-info">(Last Saved)</span>
                                @endif
                                {{ $temperatureResult['city']. ", " . $temperatureResult['region'] . ", " . $temperatureResult['country'] }} 
                            </h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
        @endif

        @if ($temperatureResult['error'] && $temperatureResult['message'])
        <div class="alert alert-danger d-flex justify-content-center">
            {{ $temperatureResult['message'] ?? 'Something went try, try again.' }}
        </div>
        @endif
    </div>
    @if ($temperatureResult['current_temp'])
        <div class="float-right">
            <form id="form_forecast">
                <input type="hidden" name="city" value="{{ $temperatureResult['city'] }}" />
                <input type="hidden" name="region" value="{{ $temperatureResult['region'] }}" />
                <input type="hidden" name="country" value="{{ $temperatureResult['country'] }}" />
                <input type="hidden" name="temperature_c" value="{{ $temperatureResult['current_temp'] }}" />
                <input type="hidden" name="cloud" value="{{ $temperatureResult['cloud'] }}" />
                <input type="hidden" name="img" value="{{ $temperatureResult['img'] }}" />
            </form>
            @if (!isset($temperatureResult['is_cache']))
            <button type="button" class="btn btn-sm btn-primary" id="save">
                <span id="btntxt">Save Forecast</span>
                <span id="btnloader" class="d-none"><i class="fa fa-spinner fa-spin" aria-hidden="true"></i></span>
            </button>
            @endif
        </div>
    @endif
</div>

@push('after-scripts')
    <script type="text/javascript">
        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("body").on("click", "button#save", function(e) {
                e.preventDefault();

                $.ajax({
                    type : "post",
                    url : "{{ route('store') }}",
                    data : $("form#form_forecast").serialize(),
                    beforeSend : function() {
                        $("#btntxt").addClass("d-none");
                        $("#btnloader").removeClass("d-none");
                    },
                    success : function(response) {
                        
                        if (response.error == false) {
                            alert("Saved");
                        } else {
                            alert(response.message);
                        }
                    },
                    error : function (response) {
                        alert("There is an error in saving the forecast.");
                    },
                    complete : function() {
                        $("#btnloader").addClass("d-none");
                        $("#btntxt").removeClass("d-none");
                    }
                })

            });
        });
    </script>
@endpush

@endsection