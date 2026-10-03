@extends('layouts.app') @section('title','إضافة قسم') @section('content') @include('departments.form',['department'=>null,'action'=>route('departments.store'),'method'=>'POST']) @endsection
