@extends('layouts.app') @section('title','تسجيل حضور') @section('content') @include('attendance.form',['attendance'=>null,'action'=>route('attendance.store'),'method'=>'POST']) @endsection
