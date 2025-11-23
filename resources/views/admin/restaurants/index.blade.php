@extends('layouts.app')

@section('title', 'Kelola Restoran - Admin')

@section('content')
<div class="main-app">
    <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-shield-alt me-2"></i>Admin Panel
            </a>
            <div class="navbar-nav ms-auto">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i>Dashboard
                </a>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-light">
                <i class="fas fa-utensils me-2"></i>Kelola Restoran
            </h2>
            <button class="btn btn-primary" onclick="alert('Form tambah restoran akan muncul di sini')">
                <i class="fas fa-plus me-2"></i>Tambah Restoran
            </button>
        </div>

        <div class="card bg-dark text-light">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Lokasi</th>
                                <th>Menu</th>
                                <th>Rating</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($restaurants as $restaurant)
                            <tr>
                                <td>{{ $restaurant->id }}</td>
                                <td>{{ $restaurant->name }}</td>
                                <td><span class="badge bg-primary">{{ $restaurant->type }}</span></td>
                                <td><span class="badge bg-success">{{ $restaurant->status }}</span></td>
                                <td>{{ Str::limit($restaurant->location, 30) }}</td>
                                <td>{{ $restaurant->menus_count }} menu</td>
                                <td>
                                    <span class="badge bg-warning">
                                        <i class="fas fa-star me-1"></i>{{ $restaurant->rating }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="alert('Edit restoran #{{ $restaurant->id }}')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="if(confirm('Hapus restoran ini?')) alert('Hapus #{{ $restaurant->id }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
