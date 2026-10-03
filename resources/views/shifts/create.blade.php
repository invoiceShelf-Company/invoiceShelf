@extends('layouts.app') @section('title','إضافة وردية') @section('content') @include('shifts.form',['shift'=>null,'action'=>route('shifts.store'),'method'=>'POST']) @endsection
