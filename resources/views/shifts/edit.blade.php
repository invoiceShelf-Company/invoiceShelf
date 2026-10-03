@extends('layouts.app') @section('title','تعديل وردية') @section('content') @include('shifts.form',['shift'=>$shift,'action'=>route('shifts.update',$shift),'method'=>'PUT']) @endsection
