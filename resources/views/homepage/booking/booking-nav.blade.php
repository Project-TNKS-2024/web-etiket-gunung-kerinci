 <style>
 	.step-proses {
 		display: flex;
 		align-items: center;
 		justify-content: space-between;
 		width: 100%;
 		margin: 50px auto;
 		text-align: center;
 	}

 	.step-proses .steps {
 		display: flex;
 		flex-direction: column;
 		align-items: center;
 		flex: 1;
 		position: relative;
 	}

 	.step-proses .icon {
 		width: 30px;
 		height: 30px;
 		border-radius: 50%;
 		border: 2px solid gray;
 		display: inline-block;
 		position: relative;
 		margin-bottom: 10px;
 		background-color: white;
 	}

 	.step-proses .line {
 		border-top: 2px solid gray;
 		background-color: gray;
 		position: absolute;
 		top: 15px;
 		left: 50%;
 		width: 100%;
 		z-index: -1;
 	}

 	.step-proses .line:first-child {
 		left: 0;
 		width: 50%;
 	}

 	.step-proses .line:last-child {
 		left: 50%;
 		width: 50%;
 	}

 	.step-proses .text {
 		margin-top: 10px;
 	}

 	.tap0 {
 		border-color: blue !important;
 	}

 	.tap1 {
 		border-color: blue !important;
 		background-color: blue !important;
 	}
 </style>
 <div class="px-5 w-100 step-proses">
 	<input type="hidden" id="step" value="{{$step}}">
 	<div class="steps mb-auto">
 		<div class="icon">
 			<i class="fa-solid fa-check" style="color: #ffffff;"></i>
 		</div>
 		<div class="text">Syarat Dan Ketentuan</div>
 		<div class="line"></div>
 	</div>
 	<div class="steps mb-auto">
 		<div class="line"></div>
 		<div class="icon">
 			<i class="fa-solid fa-check" style="color: #ffffff;"></i>
 		</div>
 		<div class="text">Formulir Pendaftaran</div>
 		<div class="line"></div>
 	</div>
 	<div class="steps mb-auto">
 		<div class="line"></div>
 		<div class="icon">
 			<i class="fa-solid fa-check" style="color: #ffffff;"></i>
 		</div>
 		<div class="text">Pembayaran</div>
 	</div>
 </div>

 <script>
 	const icon = document.querySelectorAll('.icon');
 	const line = document.querySelectorAll('.line');

 	function updateSteps(status) {
 		// reset class
 		icon.forEach(e => {
 			e.classList.remove('tap0');
 			e.classList.remove('tap1');
 		});
 		line.forEach(e => {
 			e.classList.remove('tap0');
 		});

 		if (status == 0) {
 			icon[0].classList.add('tap0');
 		} else if (status == 1) {
 			icon[0].classList.add('tap1');
 			line[0].classList.add('tap0');
 			line[1].classList.add('tap0');
 			icon[1].classList.add('tap0');
 		} else if (status == 2) {
 			icon[0].classList.add('tap1');
 			line[0].classList.add('tap0');
 			line[1].classList.add('tap0');
 			icon[1].classList.add('tap1');
 		} else if (status == 3) {
 			icon[0].classList.add('tap1');
 			line[0].classList.add('tap0');
 			line[1].classList.add('tap0');
 			icon[1].classList.add('tap1');
 			line[2].classList.add('tap0');
 			line[3].classList.add('tap0');
 			icon[2].classList.add('tap0');
 		} else if (status == 4) {
 			icon[0].classList.add('tap1');
 			line[0].classList.add('tap0');
 			line[1].classList.add('tap0');
 			icon[1].classList.add('tap1');
 			line[2].classList.add('tap0');
 			line[3].classList.add('tap0');
 			icon[2].classList.add('tap1');
 		}
 	};
 	const step = parseInt(document.getElementById('step').value);
 	updateSteps(step);
 </script>