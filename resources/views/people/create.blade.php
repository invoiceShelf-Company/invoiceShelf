@extends('layouts.app') @section('title','إضافة شخص') @section('content') @include('people.form',['person'=>null,'action'=>route('people.store'),'method'=>'POST']) @endsection
