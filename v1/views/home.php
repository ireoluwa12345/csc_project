<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Plate Recognizer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-image: linear-gradient(120deg, #f6d365 0%, #fda085 100%);
            color: white;
            font-family: "Poppins", sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        h1 {
            margin-bottom: 30px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .container {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
        }

        .image-display {
            margin-top: 20px;
            text-align: center;
        }

        .image-display img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 10px;
            border: 2px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .plate-number {
            margin-top: 20px;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            background: rgba(0, 0, 0, 0.2);
            padding: 10px;
            border-radius: 10px;
            word-wrap: break-word;
        }

        .btn-upload {
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <h1>License Plate Recognizer</h1>
    <div class="container">
        <form id="upload-form">
            <div class="mb-3">
                <label for="imageInput" class="form-label">Upload an image:</label>
                <input type="file" class="form-control" id="imageInput" accept="image/*">
            </div>
            <button type="button" class="btn btn-primary btn-upload" id="uploadButton">Upload Image</button>
        </form>
        <div class="image-display" id="imageDisplay">
            <p>No image uploaded yet.</p>
        </div>
        <div class="plate-number" id="plateNumber">
            Plate number will appear here.
        </div>
    </div>

    <script>
        const imageInput = document.getElementById('imageInput');
        const imageDisplay = document.getElementById('imageDisplay');
        const plateNumber = document.getElementById('plateNumber');
        const uploadButton = document.getElementById('uploadButton');

        uploadButton.addEventListener('click', () => {
            uploadButton.disabled = true;
            uploadButton.innerHTML += ` <i class="fa-solid fa-spinner fa-spin" id="spinner"></i>`
            const file = imageInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const param = {
                        "image": e.target.result
                    };
                    console.log(param);

                    // Corrected fetch request
                    fetch(`/infer`, {
                            "method": "POST",
                            "headers": {
                                "Content-Type": "application/json"
                            },
                            "body": JSON.stringify(param) // Send JSON stringified data directly
                        })
                        .then(response => {
                            return response.json();
                        })
                        .then(data => {
                            if (data.status == "success") {
                                imageDisplay.innerHTML = `<img src="${data.image}" alt="Uploaded Image">`;

                                fetch('https://api.ocr.space/parse/image', {
                                        method: 'POST',
                                        headers: {
                                            'apikey': 'K85380135988957'
                                        },
                                        body: new URLSearchParams({
                                            base64Image: data.image, // Your base64 image
                                            language: 'eng'
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        setTimeout(() => {
                                            plateNumber.textContent = data.ParsedResults[0].ParsedText;
                                        }, 1000);
                                    })
                                    .catch(err => console.error(err));
                            }
                            uploadButton.disabled = false;
                            var spinner = document.getElementById('spinner');
                            spinner.remove()
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                };

                reader.readAsDataURL(file);
            } else {
                alert('Please select an image to upload.');
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>

</html>

<!-- imageDisplay.innerHTML = `<img src="${e.target.result}" alt="Uploaded Image">`; -->
<!-- 
setTimeout(() => {
                    plateNumber.textContent = 'ABC-1234'; // Example recognized plate number
                }, 1000); -->