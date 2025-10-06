<!DOCTYPE html>
<html lang="id">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Mengarahkan ke Aplikasi TNKAS...</title>
   <style>
      body {
         font-family: 'Inter', Arial, sans-serif;
         background: linear-gradient(135deg, #0a5a44, #007a63);
         color: #fff;
         display: flex;
         flex-direction: column;
         justify-content: center;
         align-items: center;
         height: 100vh;
         margin: 0;
         text-align: center;
         padding: 20px;
      }

      h1 {
         font-size: 1.8rem;
         margin-bottom: 0.5rem;
      }

      p {
         font-size: 1rem;
         opacity: 0.9;
         margin-bottom: 1.5rem;
      }

      .buttons {
         display: flex;
         gap: 1rem;
         flex-wrap: wrap;
         justify-content: center;
      }

      a.button {
         background: #fff;
         color: #007a63;
         padding: 12px 22px;
         border-radius: 8px;
         font-weight: 600;
         text-decoration: none;
         box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
         transition: all 0.25s ease;
      }

      a.button:hover {
         background: #007a63;
         color: #fff;
         transform: translateY(-2px);
      }

      .spinner {
         border: 3px solid rgba(255, 255, 255, 0.3);
         border-top: 3px solid #fff;
         border-radius: 50%;
         width: 40px;
         height: 40px;
         animation: spin 1s linear infinite;
         margin-bottom: 1rem;
      }

      @keyframes spin {
         0% {
            transform: rotate(0deg);
         }

         100% {
            transform: rotate(360deg);
         }
      }

      footer {
         position: absolute;
         bottom: 15px;
         font-size: 0.8rem;
         opacity: 0.7;
      }
   </style>

   <script>
      window.onload = function() {
         const appUrl = `{{$url}}`;
         // Arahkan otomatis ke aplikasi
         window.location.href = appUrl;

         // Jika aplikasi tidak terinstal, arahkan ke halaman download
         // setTimeout(() => {
         //    window.location.href = '/download';
         // }, 2500);
      };
   </script>
</head>

<body>
   <div class="spinner"></div>
   <h1>Mengarahkan ke Aplikasi TNKAS...</h1>
   <p>Mohon tunggu sebentar. Jika tidak otomatis, gunakan tombol di bawah.</p>

   <div class="buttons">
      <a href="{{$url}}" class="button">Buka Aplikasi</a>
      <a href="/download" class="button">Download Aplikasi</a>
   </div>

   <footer>© {{ date('Y') }} TNKAS</footer>
</body>

</html>