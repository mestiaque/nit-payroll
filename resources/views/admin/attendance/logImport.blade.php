@extends(adminTheme().'layouts.app')
@section('title')
<title>{{ websiteTitle('Import ZKTeco Attendance') }}</title>
@endsection

@push('css')
<style>
    .import-container {
        max-width: 700px;
        margin: 40px auto;
    }
    .upload-area {
        border: 2px dashed #56d2ff;
        border-radius: 15px;
        padding: 40px;
        text-align: center;
        background: #f8fdff;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }
    .upload-area:hover, .upload-area.dragover {
        background: #eefbff;
        border-color: #007bff;
    }
    .upload-icon {
        font-size: 50px;
        color: #56d2ff;
        margin-bottom: 15px;
    }
    #attendance_file {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }
    .file-info {
        margin-top: 15px;
        display: none;
        font-weight: 600;
        color: #333;
    }
    .progress-wrapper {
        display: none;
        margin-top: 20px;
    }
    .progress {
        height: 10px;
        border-radius: 5px;
    }
    .btn-import {
        background: #56d2ff;
        border: none;
        color: #fff;
        padding: 10px 30px;
        font-weight: 600;
        border-radius: 25px;
        transition: 0.3s;
    }
    .btn-import:hover {
        background: #33b5e5;
        box-shadow: 0 4px 12px rgba(86, 210, 255, 0.4);
    }
    .instruction-card {
        background: #fff;
        border-radius: 10px;
        padding: 15px;
        border-left: 4px solid #56d2ff;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('contents')
@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="container-fluid">
    <div class="import-container">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white">
                <h4 class="card-title text-center">Import ZKTeco Machine Log</h4>
            </div>
            <div class="card-body">
                
                <!-- Instructions -->
                <div class="instruction-card shadow-sm">
                    <h6><i class="feather icon-info mr-1"></i> নির্দেশাবলী:</h6>
                    <small class="text-muted">
                        ১. মেশিন থেকে সংগৃহীত .txt বা .dat ফাইলটি আপলোড করুন। <br>
                        ২. ডাটা ফরম্যাট হতে হবে: ID, Date, Time, Terminal ID।
                    </small>
                </div>

                <form id="uploadForm" action="{{ route('admin.importZktecoAction') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Drag & Drop Area -->
                    <div class="upload-area" id="dropArea">
                        <input type="file" name="attendance_file" id="attendance_file" accept=".txt,.dat">
                        <div class="upload-icon">
                            <i class="feather icon-upload-cloud"></i>
                        </div>
                        <h5>Drag & Drop your file here</h5>
                        <p class="text-muted">or click to browse from computer</p>
                        
                        <div id="fileInfo" class="file-info">
                            <i class="feather icon-file text-primary"></i> <span id="fileName"></span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress-wrapper" id="progressWrapper">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Processing...</small>
                            <small id="progressPercent">0%</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                                 id="progressBar" role="progressbar" style="width: 0%"></div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-import shadow-sm" id="submitBtn">
                            <i class="feather icon-check-circle mr-1"></i> Start Import
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('js')
<script>
    const dropArea = document.getElementById('dropArea');
    const fileInput = document.getElementById('attendance_file');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const uploadForm = document.getElementById('uploadForm');
    const progressWrapper = document.getElementById('progressWrapper');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');

    // highlight drop area
    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropArea.classList.add('dragover');
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropArea.classList.remove('dragover');
        }, false);
    });

    // handle file select
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            fileName.textContent = this.files[0].name;
            fileInfo.style.display = 'block';
        }
    });

    // Form Submit with Fake Progress for UX
    uploadForm.onsubmit = function() {
        document.getElementById('submitBtn').disabled = true;
        progressWrapper.style.display = 'block';
        let width = 0;
        let interval = setInterval(() => {
            if (width >= 90) {
                clearInterval(interval);
            } else {
                width += 5;
                progressBar.style.width = width + '%';
                progressPercent.textContent = width + '%';
            }
        }, 100);
    };
</script>
@endpush
