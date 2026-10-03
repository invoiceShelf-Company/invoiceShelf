@extends('layouts.app') @section('title','إضافة منشأة') @section('content') @include('facilities.form',['facility'=>null,'action'=>route('facilities.store'),'method'=>'POST']) @endsection
