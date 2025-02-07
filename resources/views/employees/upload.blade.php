<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Employee Records</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #dfe9f3, #ffffff);
            display: flex;
            justify-content: center;
            /* align-items: center; */
            margin: 12px 0;
            min-height: 95vh;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            background: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .heading h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }

        form {
            width: 100%;
        }
        .fileInput {
            width: 80%;
            margin: 0 auto;
            background: #f8fbff;
            border: 2px dashed #a0c4ff;
            border-radius: 12px;
            padding: 20px;
            transition: 0.3s ease;
            text-align: center;
            cursor: pointer;
        }

        .fileInput:hover {
            border-color: #749bc2;
        }

        .file-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
        }

        .file-upload input[type="file"] {
            display: none;
        }

        .svgIcon img {
            width: 40px;
            height: 40px;
            margin-bottom: 10px;
        }

        .file-text {
            font-size: 15px;
            color: #555;
        }

        .upd-btn {
            margin-top: 16px;
            padding: 10px 25px;
            border: none;
            background: #4a90e2;
            color: white;
            font-size: 14px;
            border-radius: 25px;
            cursor: pointer;
            transition: 0.3s;
        }

        .upd-btn:hover {
            background: #357ab8;
        }

        .tableContainer {
            width: 80%;
            margin-top: 32px;
            text-align: center;
        }

        .tableContainer span {
            font-size: 18px;
            font-weight: 600;
            color: #444;
            display: block;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #f8f8f8;
            border-radius: 10px;
            overflow: hidden;
        }

        thead {
            background: #4a90e2;
            color: white;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        tbody tr:nth-child(even) {
            background: #eef3f7;
        }

        tbody tr:hover {
            background: #d9eaff;
        }

        th {
            text-transform: uppercase;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="heading">
            <h2>Upload Employee Records</h2>
        </div>
        <form action="{{ route('employees.import') }}" method="POST" enctype="multipart/form-data">
         @csrf
         
        <div class="fileInput" id="dropArea">
            <label class="file-upload">
                <input type="file" name="employee_file" required>
                <span class="svgIcon">
                    <img alt="Upload Icon" src="{{asset('images/uploadIcon.svg')}}">

                </span>
                <span class="file-text" id="fileText">Drag & drop or click to choose files</span>
                <!-- <small>Max file size: 10 MB</small> -->
            </label>
            <button class="upd-btn">Upload</button>
        </div>
       </form>


       <div class="tableContainer">
            @if (session('success'))
                <p>{{ session('success') }}</p>
            @endif

            @if (session('emails_to_delete'))
                <h4>Employees to Delete</h4>
                <table border="1" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Employee Code</th>
                            <th>Official Email</th>
                            <th>Designation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (session('emails_to_delete') as $employee)
                            <tr>
                                <td>{{ $employee['employee_name'] }}</td>
                                <td>{{ $employee['employee_code'] }}</td>
                                <td>{{ $employee['official_email_id'] }}</td>
                                <td>{{ $employee['designation'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if (session('emails_to_create'))
                <h4>Employees to Create</h4>
                <table border="1" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Employee Code</th>
                            <th>Official Email</th>
                            <th>Designation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (session('emails_to_create') as $employee)
                            <tr>
                                <td>{{ $employee['employee_name'] }}</td>
                                <td>{{ $employee['employee_code'] }}</td>
                                <td>{{ $employee['official_email_id'] }}</td>
                                <td>{{ $employee['designation'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            @if (session('emails_to_block'))
                <h4>Employees to Block</h4>
                <table border="1" cellpadding="10">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Employee Code</th>
                            <th>Official Email</th>
                            <th>Designation</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach (session('emails_to_block') as $employee)
                            <tr>
                                <td>{{ $employee['employee_name'] }}</td>
                                <td>{{ $employee['employee_code'] }}</td>
                                <td>{{ $employee['official_email_id'] }}</td>
                                <td>{{ $employee['designation'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>


    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const dropArea = document.getElementById("dropArea");
            const fileInput = document.getElementById("fileInput");
            const fileText = document.getElementById("fileText");

            // Prevent default browser behavior on drag
            dropArea.addEventListener("dragover", (event) => {
                event.preventDefault();
                dropArea.style.borderColor = "#357ab8";
            });

            dropArea.addEventListener("dragleave", () => {
                dropArea.style.borderColor = "#a0c4ff";
            });

            dropArea.addEventListener("drop", (event) => {
                event.preventDefault();
                dropArea.style.borderColor = "#a0c4ff";
                if (event.dataTransfer.files.length > 0) {
                    const fileName = event.dataTransfer.files[0].name;
                    fileText.textContent = `Uploaded: ${fileName}`;
                }
            });

            // Change text when file is selected via input
            fileInput.addEventListener("change", () => {
                if (fileInput.files.length > 0) {
                    fileText.textContent = `Uploaded: ${fileInput.files[0].name}`;
                }
            });
        });
    </script>
</body>

</html>
