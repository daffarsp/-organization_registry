<x-layouts.public title="Form Pendaftaran - Organization Registration System">
    <section class="bg-white py-12 sm:py-16">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.82fr_1.18fr] lg:px-8">
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                    <x-heroicon-o-arrow-left class="h-4 w-4" />
                    Kembali ke beranda
                </a>
                <h1 class="mt-6 text-3xl font-semibold text-zinc-950">Form Pendaftaran Anggota</h1>
                <p class="mt-4 text-base leading-8 text-zinc-600">
                    Isi data dengan benar. Setelah berhasil, sistem akan membuat nomor pendaftaran otomatis dan membuka pertanyaan dasar divisi.
                </p>
                <div class="mt-8 rounded-lg border border-emerald-200 bg-emerald-50 p-5">
                    <x-heroicon-o-shield-check class="h-7 w-7 text-emerald-700" />
                    <h2 class="mt-4 font-semibold text-emerald-950">Data aman untuk proses seleksi</h2>
                    <p class="mt-2 text-sm leading-6 text-emerald-900">Informasi yang dikirim akan masuk ke panel admin dan dipakai untuk review pendaftaran.</p>
                </div>
            </aside>

            <form action="{{ route('registrations.store') }}" method="POST" class="rounded-lg border border-zinc-200 bg-zinc-50 p-5 shadow-sm sm:p-6">
                @csrf

                <div class="grid gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="name" class="text-sm font-semibold text-zinc-900">Nama Lengkap <span class="text-red-600">*</span></label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="120" class="mt-2 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        <x-input-error :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <label for="email" class="text-sm font-semibold text-zinc-900">Email <span class="text-red-600">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255" class="mt-2 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        <x-input-error :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <label for="phone" class="text-sm font-semibold text-zinc-900">Nomor WhatsApp <span class="text-red-600">*</span></label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}" required maxlength="30" class="mt-2 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        <x-input-error :messages="$errors->get('phone')" />
                    </div>

                    <div>
                        <label for="gender" class="text-sm font-semibold text-zinc-900">Jenis Kelamin</label>
                        <select id="gender" name="gender" class="mt-2 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            <option value="">Pilih jika ingin mengisi</option>
                            <option value="male" @selected(old('gender') === 'male')>Laki-laki</option>
                            <option value="female" @selected(old('gender') === 'female')>Perempuan</option>
                            <option value="other" @selected(old('gender') === 'other')>Lainnya</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" />
                    </div>

                    <div>
                        <label for="birth_date" class="text-sm font-semibold text-zinc-900">Tanggal Lahir</label>
                        <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date') }}" class="mt-2 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        <x-input-error :messages="$errors->get('birth_date')" />
                    </div>

                    <div>
                        <label for="school" class="text-sm font-semibold text-zinc-900">Asal Sekolah/Kampus</label>
                        <input id="school" name="school" type="text" value="{{ old('school') }}" maxlength="150" class="mt-2 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                        <x-input-error :messages="$errors->get('school')" />
                    </div>

                    <div>
                        <label for="instagram" class="text-sm font-semibold text-zinc-900">Instagram</label>
                        <div class="mt-2 flex rounded-md border border-zinc-300 bg-white focus-within:border-emerald-500 focus-within:ring-2 focus-within:ring-emerald-100">
                            <span class="border-r border-zinc-200 px-3 py-2 text-sm text-zinc-500">@</span>
                            <input id="instagram" name="instagram" type="text" value="{{ old('instagram') }}" maxlength="100" class="w-full rounded-r-md bg-transparent px-3 py-2 text-sm outline-none">
                        </div>
                        <x-input-error :messages="$errors->get('instagram')" />
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="text-sm font-semibold text-zinc-900">Alamat</label>
                        <textarea id="address" name="address" rows="3" maxlength="1000" class="mt-2 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">{{ old('address') }}</textarea>
                        <x-input-error :messages="$errors->get('address')" />
                    </div>

                    <div class="sm:col-span-2">
                        <label for="division_id" class="text-sm font-semibold text-zinc-900">Divisi yang Diminati <span class="text-red-600">*</span></label>
                        <select id="division_id" name="division_id" required class="mt-2 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">
                            <option value="">Pilih divisi</option>
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}" @selected((string) old('division_id') === (string) $division->id)>{{ $division->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('division_id')" />
                    </div>

                    <div class="sm:col-span-2">
                        <label for="reason" class="text-sm font-semibold text-zinc-900">Alasan Bergabung <span class="text-red-600">*</span></label>
                        <textarea id="reason" name="reason" rows="5" required maxlength="2000" class="mt-2 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">{{ old('reason') }}</textarea>
                        <x-input-error :messages="$errors->get('reason')" />
                    </div>

                    <div class="sm:col-span-2">
                        <label for="organization_experience" class="text-sm font-semibold text-zinc-900">Pengalaman Organisasi</label>
                        <textarea id="organization_experience" name="organization_experience" rows="4" maxlength="2000" class="mt-2 w-full rounded-md border border-zinc-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100">{{ old('organization_experience') }}</textarea>
                        <x-input-error :messages="$errors->get('organization_experience')" />
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-zinc-600">Field bertanda <span class="text-red-600">*</span> wajib diisi.</p>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                        <x-heroicon-o-paper-airplane class="h-5 w-5" />
                        Kirim Pendaftaran
                    </button>
                </div>
            </form>
        </div>
    </section>
</x-layouts.public>
