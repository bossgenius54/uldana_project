@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-2">
            <video width="100%" height="auto" controls="controls" >
                <source src="{{  Config::get('settings.media_server_url') }}" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
                Тег video не поддерживается вашим браузером.
                <a href="video/duel.mp4">Скачайте видео</a>.
            </video>
            <br>
            <br>
            <br>
        </div>
    </div>
    <div class="row">
        @foreach($courses as $course)
            <div class="col-md-4">
                <div class="card card-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header bg-info-active">
                        <h3 class="widget-user-username">{{ $course->name }}</h3>
                        <h5 class="widget-user-desc">{{ $course->teacher->name }}</h5>
                    </div>
                    <div class="widget-user-image">
{{--                        <img class="img-circle elevation-2" src="{{ $course->teacher->avatar }}" alt="User Avatar">--}}
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $course->students()->count() }}</h5>
                                    <span class="description-text">Оқушылар</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <a href="{{route('courses.show', $course->id)}}" class="btn"><i class="icon far fa-2x fa-eye text-green"></i></a>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4">
                                <div class="description-block">
                                    <h5 class="description-header">{{ $course->lessons->count() }}</h5>
                                    <span class="description-text">Сабақ</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
                <!-- /.col -->
            </div>
        @endforeach
    </div>
</div>
@endsection
