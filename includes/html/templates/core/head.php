<?php
  if ( !Auth::get_auth_info() )
    header("Location: /login");
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="generator" content="Hugo 0.108.0">
    <title>{{page_title}}</title>
    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" type="text/css" href="/assets/plugins/datatables/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/plugins/bootstrap5/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="/assets/plugins/sweetalert2/sweetalert2.min.css"/>
    <meta name="theme-color" content="#712cf9">
    <style>
      .modal-lg, .modal, .modal-dialog{
      min-width: 90% !important;
      max-width: 90% !important;
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="/assets/css/dashboard.css" rel="stylesheet">
  </head>