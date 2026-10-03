@extends('layouts.app') @section('title','تعديل الشخص') @section('content') @include('people.form',['person'=>$person,'action'=>route('people.update',$person),'method'=>'PUT']) @endsection
