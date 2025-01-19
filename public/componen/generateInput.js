// Fungsi untuk menghasilkan string acak
function generateRandomString(length) {
   const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   let result = '';
   for (let i = 0; i < length; i++) {
      result += characters.charAt(Math.floor(Math.random() * characters.length));
   }
   return result;
}

// Fungsi untuk menghasilkan angka acak
function generateNumber(maxDigits) {
   const min = Math.pow(10, maxDigits - 1); // Minimum value (e.g., 100 for 3 digits)
   const max = Math.pow(10, maxDigits) - 1; // Maximum value (e.g., 999 for 3 digits)
   return Math.floor(Math.random() * (max - min + 1)) + min;
}

// Fungsi untuk menghasilkan nomor telepon acak
function generateRandomPhone() {
   const prefix = ['081', '082', '085', '087', '089']; // Prefix telepon umum di Indonesia
   const randomPrefix = prefix[Math.floor(Math.random() * prefix.length)];
   const randomNumber = generateNumber(8); // Menghasilkan 8 digit angka acak
   return `${randomPrefix}${randomNumber}`;
}

// Fungsi untuk menghasilkan tanggal acak dalam format YYYY-MM-DD
function generateRandomDate() {
   const start = new Date(2000, 0, 1); // Tanggal awal: 1 Januari 2000
   const end = new Date(); // Tanggal akhir: hari ini
   const randomDate = new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()));
   const year = randomDate.getFullYear();
   const month = String(randomDate.getMonth() + 1).padStart(2, '0'); // Bulan dengan format 2 digit
   const day = String(randomDate.getDate()).padStart(2, '0'); // Tanggal dengan format 2 digit
   return `${year}-${month}-${day}`;
}

// Ambil semua elemen form di dalam dokumen
const listForm = document.getElementsByTagName('form');

function generateinput() {

   // Iterasi setiap form
   Array.from(listForm).forEach(form => {
      // Ambil semua elemen input di dalam form
      const inputs = form.getElementsByTagName('input');

      // Iterasi setiap input dalam form
      Array.from(inputs).forEach(input => {
         // Periksa apakah input bukan type="hidden" dan tidak readonly
         if (input.type !== 'hidden' && !input.readOnly) {
            // Cek type inputan
            switch (input.type) {
               case 'text':
               case 'email':
               case 'password':
                  input.value = generateRandomString(8);
                  break;
               case 'number':
                  input.value = generateNumber(11);
                  break;
               case 'tel':
                  input.value = generateRandomPhone();
                  break;
               case 'date':
                  input.value = generateRandomDate();
                  break;
               case 'checkbox':
               case 'radio':
                  input.checked = true; // Randomly check/uncheck
                  break;

            }

            // Generate inputan event
            const event = new Event('input', {
               bubbles: true,
               cancelable: true,
            });
            input.dispatchEvent(event);
         }
      });
   });

}