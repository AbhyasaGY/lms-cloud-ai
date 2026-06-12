<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $course->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h3 class="text-2xl font-bold mb-4">Materi Pembelajaran</h3>

                {{-- Kita gunakan paragraf panjang buatan untuk simulasi materi yang akan diringkas AI --}}
                <div id="materi-teks" class="text-gray-700 leading-relaxed mb-6">
                    Cloud computing adalah pengiriman layanan komputasi, termasuk server, penyimpanan, database,
                    jaringan, perangkat lunak, analitik, dan kecerdasan, melalui Internet ("cloud") untuk menawarkan
                    inovasi yang lebih cepat, sumber daya yang fleksibel, dan skala ekonomis. Anda biasanya hanya
                    membayar untuk layanan cloud yang Anda gunakan, membantu menurunkan biaya operasi, menjalankan
                    infrastruktur Anda dengan lebih efisien, dan menskalakan sesuai dengan perubahan kebutuhan bisnis
                    Anda. Model deployment utamanya meliputi public cloud, private cloud, dan hybrid cloud.
                </div>

                @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
                @endif

                <div class="my-6 p-6 bg-gray-50 border rounded-lg">
                    <h4 class="text-lg font-bold mb-4">📚 Modul Pembelajaran</h4>

                    @if($course->module_url)
                    <div class="mb-4">
                        <a href="{{ $course->module_url }}" target="_blank"
                            class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition">
                            ⬇️ Download / Lihat Modul
                        </a>
                    </div>
                    @else
                    <p class="text-sm text-gray-500 mb-4">Belum ada modul yang diunggah untuk kelas ini.</p>
                    @endif

                    <hr class="my-4">

                    <form action="{{ route('courses.upload', $course->id) }}" method="POST"
                        enctype="multipart/form-data" class="flex items-center space-x-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unggah Modul Baru
                                (PDF/MP4)</label>
                            <input type="file" name="module_file" accept=".pdf, .mp4" required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <button type="submit"
                            class="bg-gray-800 hover:bg-black text-white font-bold py-2 px-4 rounded transition mt-5">
                            Upload ke Azure
                        </button>
                    </form>
                </div>

                <hr class="my-6">

                <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
                    <h4 class="text-lg font-bold text-blue-800 mb-2">✨ AI Assistant</h4>
                    <p class="text-sm text-blue-600 mb-4">Terlalu panjang? Biarkan AI merangkum materi ini untukmu.</p>

                    <button id="btn-ringkas"
                        class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded transition">
                        Buat Ringkasan Otomatis
                    </button>

                    <div id="loading-indicator" class="hidden mt-4 text-blue-600 font-semibold animate-pulse">
                        AI sedang membaca dan merangkum... mohon tunggu sebentar.
                    </div>

                    <div id="hasil-ringkasan"
                        class="hidden mt-4 p-4 bg-white border border-gray-200 rounded-md shadow-inner text-gray-800">
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
    document.getElementById('btn-ringkas').addEventListener('click', function() {
        let btn = this;
        let materiText = document.getElementById('materi-teks').innerText;
        let loading = document.getElementById('loading-indicator');
        let hasilBox = document.getElementById('hasil-ringkasan');

        // Tampilkan loading, sembunyikan tombol
        btn.classList.add('hidden');
        loading.classList.remove('hidden');
        hasilBox.classList.add('hidden');

        fetch('{{ route("ai.summary") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Token keamanan wajib Laravel
                },
                body: JSON.stringify({
                    text_materi: materiText
                })
            })
            .then(response => response.json())
            .then(data => {
                // Sembunyikan loading
                loading.classList.add('hidden');
                btn.classList.remove('hidden');
                btn.innerText = 'Buat Ringkasan Ulang';

                // Tampilkan Hasil
                hasilBox.classList.remove('hidden');
                if (data.success) {
                    // Sesuaikan 'summary_text' dengan format response asli dari Hugging Face
                    hasilBox.innerHTML = '<strong>Hasil Ringkasan:</strong><br>' + data.data[0]
                        .summary_text;
                } else {
                    hasilBox.innerHTML =
                        '<span class="text-red-500">Gagal mendapatkan ringkasan dari AI.</span>';
                }
            })
            .catch(error => {
                loading.classList.add('hidden');
                btn.classList.remove('hidden');
                hasilBox.classList.remove('hidden');
                hasilBox.innerHTML = '<span class="text-red-500">Terjadi kesalahan sistem.</span>';
                console.error('Error:', error);
            });
    });
    </script>
</x-app-layout>