import os
import pptx
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN
from pptx.enum.shapes import MSO_SHAPE

def build_presentation():
    prs = pptx.Presentation()
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_layout = prs.slide_layouts[6]

    TEMPLATE_BG = 'slide_template_clean.png'

    # Palette
    COLOR_GOLD = RGBColor(212, 195, 139)       # #D4C38B template golden header
    COLOR_DARK_TEAL = RGBColor(0, 73, 82)      # #004952 ministry teal
    COLOR_NAVY = RGBColor(12, 45, 60)          # #0C2D3C deep navy
    COLOR_TEXT_MAIN = RGBColor(30, 41, 59)     # #1E293B primary text
    COLOR_TEXT_MUTED = RGBColor(100, 116, 139) # #64748B muted text
    COLOR_WHITE = RGBColor(255, 255, 255)
    COLOR_CARD_BG = RGBColor(248, 250, 252)    # #F8FAFC
    COLOR_CARD_BORDER = RGBColor(226, 232, 240)# #E2E8F0
    COLOR_ACCENT_TEAL = RGBColor(14, 116, 144) # #0E7490
    COLOR_ACCENT_BLUE = RGBColor(37, 99, 235)  # #2563EB
    COLOR_ACCENT_GREEN = RGBColor(16, 124, 65) # #107C41
    COLOR_ACCENT_AMBER = RGBColor(217, 119, 6) # #D97706
    COLOR_LIGHT_AMBER = RGBColor(254, 243, 199)# #FEF3C7

    def add_base_slide(header_title, slide_number, font_pt=17.5):
        slide = prs.slides.add_slide(blank_layout)
        slide.shapes.add_picture(TEMPLATE_BG, 0, 0, Inches(13.333), Inches(7.5))
        
        # Header title: wider textbox (9.8 inches) to guarantee 1 single line
        tb = slide.shapes.add_textbox(Inches(0.55), Inches(0.28), Inches(9.8), Inches(0.55))
        tf = tb.text_frame
        tf.word_wrap = False
        tf.margin_left = tf.margin_top = tf.margin_right = tf.margin_bottom = 0
        p = tf.paragraphs[0]
        p.text = header_title.upper()
        p.font.name = 'Arial'
        p.font.size = Pt(font_pt)
        p.font.bold = True
        p.font.color.rgb = COLOR_GOLD
        
        # Slide number in bottom-right polygon
        tb_num = slide.shapes.add_textbox(Inches(12.75), Inches(7.08), Inches(0.5), Inches(0.35))
        tf_num = tb_num.text_frame
        tf_num.word_wrap = False
        tf_num.margin_left = tf_num.margin_top = tf_num.margin_right = tf_num.margin_bottom = 0
        p_num = tf_num.paragraphs[0]
        p_num.alignment = PP_ALIGN.CENTER
        p_num.text = str(slide_number)
        p_num.font.name = 'Arial'
        p_num.font.size = Pt(13)
        p_num.font.bold = True
        p_num.font.color.rgb = COLOR_WHITE
        
        return slide

    def add_slide_header(slide, title, subtitle=None):
        tb = slide.shapes.add_textbox(Inches(0.65), Inches(1.15), Inches(12.0), Inches(0.7))
        tf = tb.text_frame
        tf.word_wrap = True
        tf.margin_left = tf.margin_top = tf.margin_right = tf.margin_bottom = 0
        
        p = tf.paragraphs[0]
        p.text = title
        p.font.name = 'Arial'
        p.font.size = Pt(20)
        p.font.bold = True
        p.font.color.rgb = COLOR_NAVY
        
        if subtitle:
            p2 = tf.add_paragraph()
            p2.text = subtitle
            p2.font.name = 'Arial'
            p2.font.size = Pt(11)
            p2.font.italic = True
            p2.font.color.rgb = COLOR_TEXT_MUTED
            p2.space_before = Pt(2)

    def add_card(slide, left, top, width, height, bg_color=COLOR_CARD_BG, border_color=COLOR_CARD_BORDER):
        shape = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, left, top, width, height)
        shape.fill.solid()
        shape.fill.fore_color.rgb = bg_color
        shape.line.color.rgb = border_color
        shape.line.width = Pt(1.2)
        return shape

    def add_bullet(tf, bold_prefix, text_content, font_size=10.5, space_after=3):
        p = tf.add_paragraph()
        p.space_before = Pt(2)
        p.space_after = Pt(space_after)
        
        r1 = p.add_run()
        r1.text = "• " + bold_prefix + " "
        r1.font.name = 'Arial'
        r1.font.size = Pt(font_size)
        r1.font.bold = True
        r1.font.color.rgb = COLOR_TEXT_MAIN
        
        r2 = p.add_run()
        r2.text = text_content
        r2.font.name = 'Arial'
        r2.font.size = Pt(font_size)
        r2.font.bold = False
        r2.font.color.rgb = COLOR_TEXT_MAIN

    # =========================================================================
    # SLIDE 1: Master Cover Slide
    # =========================================================================
    s1 = add_base_slide('LAPORAN CAPAIAN MINGGUAN - APLIKASI ATEKA', 1, font_pt=18)
    
    # Left Card: Executive Title
    add_card(s1, Inches(0.65), Inches(1.25), Inches(5.8), Inches(5.4), COLOR_WHITE, COLOR_DARK_TEAL)
    
    # Place Ministry Logo in top left of card
    if os.path.exists('extracted_f2/slide_1_pic_2_Picture 3.jpg'):
        s1.shapes.add_picture('extracted_f2/slide_1_pic_2_Picture 3.jpg', Inches(0.95), Inches(1.45), Inches(0.9), Inches(0.9))
    
    tb_inst = s1.shapes.add_textbox(Inches(2.0), Inches(1.45), Inches(4.2), Inches(0.9))
    tf_inst = tb_inst.text_frame
    tf_inst.word_wrap = True
    p = tf_inst.paragraphs[0]
    p.text = "KEMENTERIAN PERUMAHAN & KAWASAN PERMUKIMAN RI"
    p.font.name = 'Arial'
    p.font.size = Pt(9.5)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_TEAL
    p2 = tf_inst.add_paragraph()
    p2.text = "Direktorat Bina Teknik Permukiman & Perumahan\nSubbagian Tata Usaha (Kasubag TU)"
    p2.font.name = 'Arial'
    p2.font.size = Pt(8.5)
    p2.font.color.rgb = COLOR_TEXT_MUTED
    
    tb_l = s1.shapes.add_textbox(Inches(0.95), Inches(2.55), Inches(5.2), Inches(3.9))
    tf_l = tb_l.text_frame
    tf_l.word_wrap = True
    tf_l.margin_left = tf_l.margin_top = tf_l.margin_right = tf_l.margin_bottom = 0
    
    p = tf_l.paragraphs[0]
    p.text = "APLIKASI ATEKA"
    p.font.name = 'Arial'
    p.font.size = Pt(30)
    p.font.bold = True
    p.font.color.rgb = COLOR_DARK_TEAL
    
    p = tf_l.add_paragraph()
    p.text = "Aplikasi Tata Kelola Persediaan & Pengadaan ATK"
    p.font.name = 'Arial'
    p.font.size = Pt(12.5)
    p.font.bold = True
    p.font.color.rgb = COLOR_NAVY
    p.space_after = Pt(10)
    
    p = tf_l.add_paragraph()
    p.text = "Laporan Perkembangan Fitur Mingguan & Hasil Uji Coba"
    p.font.name = 'Arial'
    p.font.size = Pt(11)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_AMBER
    
    p = tf_l.add_paragraph()
    p.text = "Dokumen konsolidasi resmi capaian pengembangan sistem ATEKA yang memuat rekapitulasi sprint teknis pada Minggu Ke-3 (05 – 08 Okt 2026) serta trilogi buku panduan pengguna, standardisasi 4 alur sistem, dan evaluasi uji coba pengguna (UAT) pada Minggu Ke-4 (Tahun Anggaran 2026)."
    p.font.name = 'Arial'
    p.font.size = Pt(9.5)
    p.font.color.rgb = COLOR_TEXT_MAIN
    p.space_before = Pt(6)
    
    # Right Cards: Week 3 and Week 4 Summaries
    # Card Week 3
    add_card(s1, Inches(6.7), Inches(1.25), Inches(5.95), Inches(2.55), COLOR_CARD_BG, COLOR_ACCENT_TEAL)
    tb_w3 = s1.shapes.add_textbox(Inches(6.95), Inches(1.4), Inches(5.45), Inches(2.25))
    tf_w3 = tb_w3.text_frame
    tf_w3.word_wrap = True
    p = tf_w3.paragraphs[0]
    p.text = "BAGIAN I • MINGGU KE-3 (05 – 08 OKTOBER 2026)"
    p.font.name = 'Arial'
    p.font.size = Pt(10)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_TEAL
    
    p = tf_w3.add_paragraph()
    p.text = "Sprint Intensif 4 Hari: Transformasi Arsitektur & Fitur Baru"
    p.font.name = 'Arial'
    p.font.size = Pt(12)
    p.font.bold = True
    p.font.color.rgb = COLOR_NAVY
    p.space_after = Pt(4)
    
    add_bullet(tf_w3, "Pemisahan Alur:", "Form khusus usulan beli baru & antrean verifikasi Kasubag TU.", font_size=10)
    add_bullet(tf_w3, "Tata Kelola ASN:", "Integrasi NIP resmi, kelola PIC unit, & self-service reset password.", font_size=10)
    add_bullet(tf_w3, "Notifikasi & Bukti:", "Icon lonceng navbar real-time & gelar resmi ASN pada nota dinas.", font_size=10)
    add_bullet(tf_w3, "Penyekatan Divisi:", "PIC distribusi per unit (SDK, BM, PJF, TU) & upload bukti foto.", font_size=10)

    # Card Week 4
    add_card(s1, Inches(6.7), Inches(4.0), Inches(5.95), Inches(2.65), COLOR_CARD_BG, COLOR_NAVY)
    tb_w4 = s1.shapes.add_textbox(Inches(6.95), Inches(4.15), Inches(5.45), Inches(2.35))
    tf_w4 = tb_w4.text_frame
    tf_w4.word_wrap = True
    p = tf_w4.paragraphs[0]
    p.text = "BAGIAN II • MINGGU KE-4 (12 – 16 OKTOBER 2026)"
    p.font.name = 'Arial'
    p.font.size = Pt(10)
    p.font.bold = True
    p.font.color.rgb = COLOR_NAVY
    
    p = tf_w4.add_paragraph()
    p.text = "Buku Panduan Pengguna, Tampilan Sistem, Alur, & Uji Coba (UAT)"
    p.font.name = 'Arial'
    p.font.size = Pt(12)
    p.font.bold = True
    p.font.color.rgb = COLOR_DARK_TEAL
    p.space_after = Pt(4)
    
    add_bullet(tf_w4, "Trilogi Manual Book:", "Penerbitan 3 buku panduan resmi Pegawai, Subdit, & PIC.", font_size=10)
    add_bullet(tf_w4, "Antarmuka Responsif:", "Dashboard visual interaktif berbasis peran pemohon & logistik.", font_size=10)
    add_bullet(tf_w4, "Arsitektur Diagram:", "Pemetaan 4 alur kerja: sistem, PIC distribusi, & pegawai mandiri.", font_size=10)
    add_bullet(tf_w4, "Hasil Uji Coba (UAT):", "Survei evaluasi pengguna instansi dengan kepuasan sangat tinggi.", font_size=10)

    # =========================================================================
    # SLIDE 2: Minggu Ke-3 — Pokok Pembaruan Sprint 4 Hari
    # =========================================================================
    s2 = add_base_slide('KEGIATAN MINGGU KE 3 - POKOK PEMBARUAN FITUR', 2, font_pt=18)
    add_slide_header(s2, "Laporan Perkembangan & Pembaruan Fitur Aplikasi ATEKA", 
                     "Sprint Intensif 4 Hari Kerja: Senin s/d Kamis (05 – 08 Oktober 2026) • Dit. Bina Teknik PKP")
    
    updates = [
        ("[01] Pemisahan Alur Pengadaan vs Stok", 
         "Usulan beli baru non-katalog terpisah dari persediaan gudang. Dilengkapi verifikasi parsial Kasubag TU & integrasi langsung faktur offline ke e-katalog.",
         COLOR_ACCENT_TEAL),
        ("[02] Tata Kelola Akun & NIP ASN", 
         "Integrasi NIP resmi pada pendaftaran dan profil pengguna. Kasubag TU mengelola PIC 4 divisi mandiri serta self-service reset password via validasi NIP.",
         COLOR_ACCENT_BLUE),
        ("[03] Notifikasi Lonceng & Cetak Bukti", 
         "Sistem notifikasi navbar real-time terpusat dengan counter unread. Penambahan gelar akademik ASN (name_with_title) demi keabsahan berita acara resmi.",
         COLOR_ACCENT_AMBER),
        ("[04] Penyekatan Multi-Divisi PIC", 
         "Isolasi hak akses PIC per unit kerja (SDK, BM, PJF, TU). Kasubag TU dibebaskan dari peran distribusi untuk fokus murni manajerial dan persetujuan.",
         COLOR_DARK_TEAL),
        ("[05] Upload Bukti Foto Fisik", 
         "Dokumentasi digital penyerahan fisik barang secara riil (WebP/JPG). Dilengkapi modal peninjau bukti tanpa beban cetak pada struk thermal.",
         COLOR_ACCENT_GREEN),
        ("[06] Kontrol Anti-Stocking ATK", 
         "Audit riwayat pengadaan dan pembatasan frekuensi klaim untuk mencegah penimbunan barang kantor yang berlebih di luar kebutuhan wajar tim kerja.",
         COLOR_NAVY)
    ]
    
    col_w = Inches(3.8)
    row_h = Inches(2.35)
    left_positions = [Inches(0.65), Inches(4.75), Inches(8.85)]
    top_positions = [Inches(2.0), Inches(4.55)]
    
    for idx, (title, desc, accent) in enumerate(updates):
        r = idx // 3
        c = idx % 3
        cx = left_positions[c]
        cy = top_positions[r]
        
        add_card(s2, cx, cy, col_w, row_h, COLOR_WHITE, accent)
        tb = s2.shapes.add_textbox(cx + Inches(0.2), cy + Inches(0.2), col_w - Inches(0.4), row_h - Inches(0.4))
        tf = tb.text_frame
        tf.word_wrap = True
        
        p = tf.paragraphs[0]
        p.text = title
        p.font.name = 'Arial'
        p.font.size = Pt(12)
        p.font.bold = True
        p.font.color.rgb = accent
        p.space_after = Pt(6)
        
        p2 = tf.add_paragraph()
        p2.text = desc
        p2.font.name = 'Arial'
        p2.font.size = Pt(10)
        p2.font.color.rgb = COLOR_TEXT_MAIN

    # =========================================================================
    # SLIDE 3: Minggu Ke-3 — Ikhtisar Eksekutif Roadmap 4 Hari
    # =========================================================================
    s3 = add_base_slide('KEGIATAN MINGGU KE 3 - IKHTISAR EKSEKUTIF SPRINT', 3, font_pt=18)
    add_slide_header(s3, "Roadmap Transformasi & Sprint 4 Hari Kerja", 
                     "Perkembangan arsitektur dan kapabilitas sistem ATEKA dari tanggal 05 hingga 08 Oktober 2026")
    
    days_data = [
        ("HARI 1 • SENIN", "05 Oktober 2026", "Fondasi Pengadaan & Rekapitulasi", COLOR_ACCENT_TEAL, [
            ("Form Usulan Beli Baru:", "Pengajuan barang habis / non-katalog dinas."),
            ("Pemisahan Antrean TU:", "Tab verifikasi Stok vs Pengadaan baru."),
            ("Persetujuan Parsial:", "Kasubag setujui kuantitas sesuai pagu anggaran."),
            ("Modul Rekapitulasi:", "Cetak Berita Acara resmi format Dit. Bina Teknik.")
        ]),
        ("HARI 2 • SELASA", "06 Oktober 2026", "Standardisasi NIP & Pengguna", COLOR_ACCENT_BLUE, [
            ("Integrasi NIP ASN:", "Kewajiban NIP pada form daftar & profil ASN."),
            ("Master Pengguna TU:", "Kasubag TU dapat menambah akun PIC & Pegawai."),
            ("Reset Password Mandiri:", "Validasi ganda 2-Factor via NIP dan Email dinas."),
            ("Modernisasi UI:", "Dialog konfirmasi interaktif berbasis SweetAlert2.")
        ]),
        ("HARI 3 • RABU", "07 Oktober 2026", "Notifikasi Real-Time & Legalitas", COLOR_ACCENT_AMBER, [
            ("Notifikasi Navbar:", "Icon lonceng terpusat dengan indikator badge merah."),
            ("Gelar Resmi ASN:", "Penambahan name_with_title demi keabsahan surat."),
            ("Sinkronisasi Stok:", "Aksi CRUD master produk otomatis update mutasi."),
            ("Penyelarasan Struk:", "Desain slip bukti pengeluaran barang lebih proporsional.")
        ]),
        ("HARI 4 • KAMIS", "08 Oktober 2026", "Penyekatan Divisi & Bukti Foto", COLOR_DARK_TEAL, [
            ("Penyekatan 4 Divisi:", "PIC SDK, BM, PJF, & TU berdiri independen."),
            ("Pencabutan Kasubag:", "Fokus murni Approver, bebas tugas penyerahan fisik."),
            ("Upload Bukti Foto:", "Dokumentasi digital serah terima fisik (WebP/JPG)."),
            ("Anti-Stocking ATK:", "Audit riwayat klaim & cegah penimbunan barang.")
        ])
    ]
    
    col_w4 = Inches(2.85)
    gap4 = Inches(0.2)
    left_start = Inches(0.65)
    top_pos = Inches(2.0)
    card_h = Inches(4.8)
    
    for i, (day_lbl, date_lbl, title_lbl, color_lbl, points) in enumerate(days_data):
        cx = left_start + i * (col_w4 + gap4)
        add_card(s3, cx, top_pos, col_w4, card_h, COLOR_WHITE, color_lbl)
        
        tb = s3.shapes.add_textbox(cx + Inches(0.18), top_pos + Inches(0.18), col_w4 - Inches(0.36), card_h - Inches(0.36))
        tf = tb.text_frame
        tf.word_wrap = True
        
        p = tf.paragraphs[0]
        p.text = day_lbl
        p.font.name = 'Arial'
        p.font.size = Pt(11)
        p.font.bold = True
        p.font.color.rgb = color_lbl
        
        p = tf.add_paragraph()
        p.text = date_lbl
        p.font.name = 'Arial'
        p.font.size = Pt(9.5)
        p.font.color.rgb = COLOR_TEXT_MUTED
        
        p = tf.add_paragraph()
        p.text = title_lbl
        p.font.name = 'Arial'
        p.font.size = Pt(11.5)
        p.font.bold = True
        p.font.color.rgb = COLOR_NAVY
        p.space_after = Pt(8)
        
        for p_bold, p_desc in points:
            add_bullet(tf, p_bold, p_desc, font_size=9.5, space_after=4)

    # =========================================================================
    # SLIDE 4: Minggu Ke-3 — Sprint Hari Ke-1 (Penjelasan)
    # =========================================================================
    s4 = add_base_slide('KEGIATAN MINGGU KE 3 - SPRINT HARI KE-1: PENGADAAN & REKAP', 4, font_pt=16.5)
    add_slide_header(s4, "Hari 1 (Senin, 05 Okt 2026): Fondasi Pengadaan & Rekapitulasi", 
                     "Membuka alur pengadaan barang baru serta memisahkan antrean verifikasi Kasubag TU")
    
    h1_cards = [
        ("Form Usulan Pengadaan (Beli Baru)", COLOR_ACCENT_TEAL, [
            ("Latar Belakang:", "Sebelumnya sistem hanya melayani barang yang siap di stok gudang."),
            ("Formulir Khusus:", "Pegawai & Subdit kini dapat mengusulkan barang baru / habis di katalog."),
            ("Parameter Detail:", "Input spesifikasi merk, estimasi harga satuan, dan kuantitas unit."),
            ("Dukungan Ganda:", "Melayani pengajuan perorangan staf maupun usulan kolektif Subdirektorat.")
        ]),
        ("Pemisahan Antrean Kasubag TU", COLOR_ACCENT_BLUE, [
            ("Kategori Terpisah:", "Menu Verifikasi dibagi: 'Persediaan Stok' vs 'Pengadaan Beli Baru'."),
            ("Verifikasi Parsial:", "Kasubag TU dapat menyetujui kuantitas sebagian (approved qty) per item."),
            ("Faktur Pembelian:", "Input nomor faktur belanja TU dan auto-registrasi ke Master Katalog."),
            ("Kontrol Penyerahan:", "Barang baru tidak masuk ke PIC sebelum selesai dibeli secara fisik.")
        ]),
        ("Modul Rekapitulasi & Cetak Resmi", COLOR_DARK_TEAL, [
            ("Dashboard Rekap:", "Rekap permohonan ATK & Suplais Komputer terpadu Kasubag & Arsiparis."),
            ("Penyaringan Fleksibel:", "Filter rentang tanggal, bulan, tahun, serta kategori pemohon."),
            ("Layout Dit. Bina Teknik:", "Format cetak resmi Kementerian PKP RI lengkap dengan kop dinas."),
            ("Otentikasi Pejabat:", "Mencantumkan tanda tangan Pengguna Barang dan Kasubag Tata Usaha.")
        ])
    ]
    
    col_w3 = Inches(3.85)
    gap3 = Inches(0.25)
    left3 = Inches(0.65)
    card_h3 = Inches(4.8)
    
    for i, (c_title, c_color, c_bullets) in enumerate(h1_cards):
        cx = left3 + i * (col_w3 + gap3)
        add_card(s4, cx, Inches(2.0), col_w3, card_h3, COLOR_WHITE, c_color)
        
        tb = s4.shapes.add_textbox(cx + Inches(0.2), Inches(2.2), col_w3 - Inches(0.4), card_h3 - Inches(0.4))
        tf = tb.text_frame
        tf.word_wrap = True
        
        p = tf.paragraphs[0]
        p.text = c_title
        p.font.name = 'Arial'
        p.font.size = Pt(13)
        p.font.bold = True
        p.font.color.rgb = c_color
        p.space_after = Pt(10)
        
        for b_title, b_desc in c_bullets:
            add_bullet(tf, b_title, b_desc, font_size=10, space_after=6)

    # =========================================================================
    # SLIDE 5: Minggu Ke-3 — Sprint Hari Ke-1 (Dokumentasi Screenshot)
    # =========================================================================
    s5 = add_base_slide('KEGIATAN MINGGU KE 3 - DOKUMENTASI SPRINT HARI KE-1', 5, font_pt=18)
    add_slide_header(s5, "Dokumentasi Antarmuka: Modul Usulan Pengadaan & Rekapitulasi", 
                     "Tangkapan layar antarmuka pengguna pada formulir pengadaan, verifikasi, dan rekapitulasi")
    
    img_h1 = [
        ('extracted_f2/slide_4_pic_8_Picture 20.png', 'Formulir Pengadaan Beli Baru', 'Input usulan nama barang, estimasi harga satuan & kuantitas'),
        ('extracted_f2/slide_4_pic_9_Picture 22.png', 'Verifikasi Kasubag TU', 'Antrean khusus persetujuan kuantitas usulan pengadaan'),
        ('extracted_f2/slide_4_pic_11_Picture 27.png', 'Rekapitulasi Permintaan ATK', 'Dashboard monitoring data permohonan ATK Dit. Bina Teknik')
    ]
    
    for i, (img_p, lbl, sub) in enumerate(img_h1):
        cx = Inches(0.9) + i * Inches(4.0)
        add_card(s5, cx, Inches(1.95), Inches(3.55), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
        if os.path.exists(img_p):
            s5.shapes.add_picture(img_p, cx + Inches(0.65), Inches(2.05), Inches(2.25), Inches(4.1))
        
        tb = s5.shapes.add_textbox(cx + Inches(0.15), Inches(6.2), Inches(3.25), Inches(0.6))
        tf = tb.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.alignment = PP_ALIGN.CENTER
        p.text = lbl
        p.font.name = 'Arial'
        p.font.size = Pt(10.5)
        p.font.bold = True
        p.font.color.rgb = COLOR_NAVY
        
        p2 = tf.add_paragraph()
        p2.alignment = PP_ALIGN.CENTER
        p2.text = sub
        p2.font.name = 'Arial'
        p2.font.size = Pt(8.5)
        p2.font.color.rgb = COLOR_TEXT_MUTED

    # =========================================================================
    # SLIDE 6: Minggu Ke-3 — Sprint Hari Ke-2 (Penjelasan)
    # =========================================================================
    s6 = add_base_slide('KEGIATAN MINGGU KE 3 - SPRINT HARI KE-2: NIP ASN & PENGGUNA', 6, font_pt=17)
    add_slide_header(s6, "Hari 2 (Selasa, 06 Okt 2026): NIP ASN & Manajemen Pengguna", 
                     "Memperkuat identitas resmi ASN, pengelolaan akun mandiri Kasubag TU, dan reset password aman")
    
    h2_cards = [
        ("Integrasi NIP & Manajemen Pengguna", COLOR_ACCENT_TEAL, [
            ("Kewajiban NIP ASN:", "Kolom NIP resmi ditambahkan pada registrasi akun baru, database, serta berita acara."),
            ("Master Pengguna TU:", "Kasubag TU memiliki wewenang membuat akun PIC Unit (SDK, BM, PJF, TU) & Pegawai."),
            ("Penataan Hak Akses:", "Menjamin setiap akun terhubung dengan unit kerja definitif dan mencegah duplikasi."),
            ("Pencegahan Anomali:", "Menolak pendaftaran ganda dan menjaga integritas database kepegawaian.")
        ]),
        ("Fitur Reset Password Mandiri Berbasis NIP", COLOR_ACCENT_BLUE, [
            ("Self-Service Recovery:", "Pengguna atau PIC yang lupa password dapat mereset mandiri tanpa hubungi admin."),
            ("Validasi Ganda (2-Factor):", "Sistem memvalidasi kombinasi Email Dinas dan NIP yang terdaftar resmi."),
            ("Keamanan Berlapis:", "Mencegah pengambilalihan akun dan meminimalisir waktu henti operasional kantor."),
            ("Instan & Otomatis:", "Kredensial baru langsung aktif setelah konfirmasi NIP tanpa menunggu antrean tiket IT.")
        ]),
        ("Penyempurnaan Antarmuka: SweetAlert2", COLOR_DARK_TEAL, [
            ("Modal Dialog Modern:", "Menggantikan dialog browser standar yang kaku dengan modal elegan SweetAlert2."),
            ("Loading State Real-Time:", "Animasi indikator proses simpan mencegah pengguna melakukan klik berulang."),
            ("Notifikasi Interaktif:", "Toast alert sukses memberi umpan balik visual yang ramah bagi pengguna dinas."),
            ("Navigasi Responsif:", "Bilah navigasi sidebar adaptif di berbagai resolusi layar desktop dan mobile.")
        ])
    ]
    
    for i, (c_title, c_color, c_bullets) in enumerate(h2_cards):
        cx = left3 + i * (col_w3 + gap3)
        add_card(s6, cx, Inches(2.0), col_w3, card_h3, COLOR_WHITE, c_color)
        
        tb = s6.shapes.add_textbox(cx + Inches(0.2), Inches(2.2), col_w3 - Inches(0.4), card_h3 - Inches(0.4))
        tf = tb.text_frame
        tf.word_wrap = True
        
        p = tf.paragraphs[0]
        p.text = c_title
        p.font.name = 'Arial'
        p.font.size = Pt(13)
        p.font.bold = True
        p.font.color.rgb = c_color
        p.space_after = Pt(10)
        
        for b_title, b_desc in c_bullets:
            add_bullet(tf, b_title, b_desc, font_size=10, space_after=6)

    # =========================================================================
    # SLIDE 7: Minggu Ke-3 — Sprint Hari Ke-2 (Dokumentasi Screenshot)
    # =========================================================================
    s7 = add_base_slide('KEGIATAN MINGGU KE 3 - DOKUMENTASI SPRINT HARI KE-2', 7, font_pt=18)
    add_slide_header(s7, "Dokumentasi Antarmuka: Reset Password & Pendaftaran ASN", 
                     "Tangkapan layar antarmuka keamanan akun dan standardisasi profil aparatur sipil negara")
    
    img_h2 = [
        ('extracted_f2/slide_6_pic_9_Picture 9.png', 'Reset Kata Sandi Mandiri', 'Verifikasi kombinasi NIP resmi dan Email kedinasan untuk pemulihan akses akun'),
        ('extracted_f2/slide_6_pic_10_Picture 11.png', 'Formulir Registrasi ASN', 'Pendaftaran akun Pegawai dan Subdirektorat dengan input NIP dan Gelar Akademik')
    ]
    
    for i, (img_p, lbl, sub) in enumerate(img_h2):
        cx = Inches(1.5) + i * Inches(5.4)
        add_card(s7, cx, Inches(1.95), Inches(4.8), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
        if os.path.exists(img_p):
            s7.shapes.add_picture(img_p, cx + Inches(1.15), Inches(2.05), Inches(2.5), Inches(4.15))
        
        tb = s7.shapes.add_textbox(cx + Inches(0.2), Inches(6.25), Inches(4.4), Inches(0.55))
        tf = tb.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.alignment = PP_ALIGN.CENTER
        p.text = lbl
        p.font.name = 'Arial'
        p.font.size = Pt(11)
        p.font.bold = True
        p.font.color.rgb = COLOR_NAVY
        
        p2 = tf.add_paragraph()
        p2.alignment = PP_ALIGN.CENTER
        p2.text = sub
        p2.font.name = 'Arial'
        p2.font.size = Pt(8.5)
        p2.font.color.rgb = COLOR_TEXT_MUTED

    # =========================================================================
    # SLIDE 8: Minggu Ke-3 — Sprint Hari Ke-3 (Penjelasan)
    # =========================================================================
    s8 = add_base_slide('KEGIATAN MINGGU KE 3 - SPRINT HARI KE-3: NOTIFIKASI & LEGALITAS', 8, font_pt=16.5)
    add_slide_header(s8, "Hari 3 (Rabu, 07 Okt 2026): Notifikasi Terpusat & Legalitas Dokumen", 
                     "Transparansi status permohonan real-time dan standardisasi gelar akademik ASN")
    
    col_w2 = Inches(5.85)
    
    # Left Card: Notifikasi
    add_card(s8, Inches(0.65), Inches(2.0), col_w2, Inches(4.8), COLOR_WHITE, COLOR_ACCENT_TEAL)
    tb_n = s8.shapes.add_textbox(Inches(0.9), Inches(2.2), col_w2 - Inches(0.5), Inches(4.4))
    tf_n = tb_n.text_frame
    tf_n.word_wrap = True
    p = tf_n.paragraphs[0]
    p.text = "Sistem Notifikasi Lonceng Terpusat (Navbar)"
    p.font.name = 'Arial'
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_TEAL
    p.space_after = Pt(10)
    add_bullet(tf_n, "Komunikasi Asinkron:", "Tabel database user_notifications mencatat setiap peristiwa tiket secara real-time.", font_size=10.5, space_after=6)
    add_bullet(tf_n, "Icon Lonceng Interaktif:", "Menampilkan badge counter unread berwarna merah di bilah navbar seluruh role.", font_size=10.5, space_after=6)
    add_bullet(tf_n, "Pemberitahuan Otomatis:", "Notifikasi langsung terkirim saat usulan disetujui, ditolak Kasubag, atau siap diambil.", font_size=10.5, space_after=6)
    add_bullet(tf_n, "Tautan Langsung (Deep Link):", "Mengklik notifikasi langsung membawa pengguna ke detail tiket terkait dan menandai status baca.", font_size=10.5, space_after=6)
    
    # Right Card: Legalitas & Stok
    add_card(s8, Inches(6.8), Inches(2.0), col_w2, Inches(4.8), COLOR_WHITE, COLOR_ACCENT_AMBER)
    tb_l = s8.shapes.add_textbox(Inches(7.05), Inches(2.2), col_w2 - Inches(0.5), Inches(4.4))
    tf_l = tb_l.text_frame
    tf_l.word_wrap = True
    p = tf_l.paragraphs[0]
    p.text = "Legalitas Dokumen & Sinkronisasi Stok Mutasi"
    p.font.name = 'Arial'
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_AMBER
    p.space_after = Pt(10)
    add_bullet(tf_l, "Standardisasi Gelar ASN:", "Menambahkan field title pada database profil. Format nama resmi: Fairuz Armina, S.T. beserta NIP.", font_size=10.5, space_after=6)
    add_bullet(tf_l, "Kesesuaian Tata Naskah:", "Berita acara dan tanda terima pengeluaran barang kini memenuhi kaidah hukum keabsahan naskah dinas.", font_size=10.5, space_after=6)
    add_bullet(tf_l, "Sinkronisasi Real-Time:", "Aksi CRUD master produk otomatis menyelaraskan histori mutasi barang masuk & saldo gudang.", font_size=10.5, space_after=6)
    add_bullet(tf_l, "Penyelarasan Struk Bukti:", "Desain struk bukti pengambilan barang disempurnakan agar rapi saat dicetak pada slip kecil.", font_size=10.5, space_after=6)

    # =========================================================================
    # SLIDE 9: Minggu Ke-3 — Sprint Hari Ke-3 (Dokumentasi Screenshot)
    # =========================================================================
    s9 = add_base_slide('KEGIATAN MINGGU KE 3 - DOKUMENTASI SPRINT HARI KE-3', 9, font_pt=18)
    add_slide_header(s9, "Dokumentasi Antarmuka: Notifikasi Real-Time Navbar & Panel Dropdown", 
                     "Pemberitahuan perubahan status permohonan yang terintegrasi langsung pada bilah navigasi")
    
    add_card(s9, Inches(0.65), Inches(1.95), Inches(7.4), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f2/slide_8_pic_8_Picture 19.jpg'):
        s9.shapes.add_picture('extracted_f2/slide_8_pic_8_Picture 19.jpg', Inches(0.85), Inches(2.1), Inches(7.0), Inches(4.0))
    tb = s9.shapes.add_textbox(Inches(0.85), Inches(6.25), Inches(7.0), Inches(0.5))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.alignment = PP_ALIGN.CENTER
    p.text = "Bilah Navigasi Navbar Terintegrasi Lonceng Notifikasi Merah"
    p.font.name = 'Arial'
    p.font.size = Pt(10.5)
    p.font.bold = True
    p.font.color.rgb = COLOR_NAVY
    
    add_card(s9, Inches(8.25), Inches(1.95), Inches(4.45), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f2/slide_8_pic_9_Picture 21.jpg'):
        s9.shapes.add_picture('extracted_f2/slide_8_pic_9_Picture 21.jpg', Inches(8.6), Inches(2.1), Inches(3.75), Inches(4.0))
    tb = s9.shapes.add_textbox(Inches(8.45), Inches(6.25), Inches(4.05), Inches(0.5))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.alignment = PP_ALIGN.CENTER
    p.text = "Panel Dropdown Rincian Usulan Menunggu Verifikasi"
    p.font.name = 'Arial'
    p.font.size = Pt(10.5)
    p.font.bold = True
    p.font.color.rgb = COLOR_NAVY

    # =========================================================================
    # SLIDE 10: Minggu Ke-3 — Sprint Hari Ke-4 (Penjelasan Penyekatan & Bukti Foto)
    # =========================================================================
    s10 = add_base_slide('KEGIATAN MINGGU KE 3 - SPRINT HARI KE-4: PENYEKATAN & BUKTI FOTO', 10, font_pt=16)
    add_slide_header(s10, "Hari 4 (Kamis, 08 Okt 2026): Penyekatan Multi-Divisi & Bukti Serah Terima", 
                     "Pemisahan zona penyerahan barang per unit kerja dan dokumentasi serah terima fisik digital")
    
    add_card(s10, Inches(0.65), Inches(2.0), col_w2, Inches(4.8), COLOR_WHITE, COLOR_DARK_TEAL)
    tb_p = s10.shapes.add_textbox(Inches(0.9), Inches(2.2), col_w2 - Inches(0.5), Inches(4.4))
    tf_p = tb_p.text_frame
    tf_p.word_wrap = True
    p = tf_p.paragraphs[0]
    p.text = "Tata Kelola Distribusi Berbasis Divisi"
    p.font.name = 'Arial'
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = COLOR_DARK_TEAL
    p.space_after = Pt(8)
    add_bullet(tf_p, "Penyekatan Ketat PIC:", "Setiap akun PIC Distribusi kini HANYA menerima dan menyerahkan barang milik divisinya.", font_size=10, space_after=5)
    add_bullet(tf_p, "Pencabutan Kasubag TU:", "Menu Distribusi Barang dihapus dari sidebar Kasubag TU agar murni fokus sebagai Approver & Pengawas.", font_size=10, space_after=5)
    add_bullet(tf_p, "PIC SUBDIT SDK:", "Khusus mengelola permohonan kolektif dari Tim Subdit Sumber Daya Konstruksi.", font_size=10, space_after=4)
    add_bullet(tf_p, "PIC SUBDIT BM / KBR:", "Khusus mengelola permohonan kolektif dari Tim Subdit Bangunan & Rumah.", font_size=10, space_after=4)
    add_bullet(tf_p, "PIC SUBDIT PJF:", "Khusus mengelola permohonan kolektif Tim Pengelolaan Jabatan Fungsional.", font_size=10, space_after=4)
    add_bullet(tf_p, "PIC SUBBAG TU:", "Mengelola permohonan internal Subbag TU dan SELURUH Pegawai Individu.", font_size=10, space_after=4)

    add_card(s10, Inches(6.8), Inches(2.0), col_w2, Inches(4.8), COLOR_WHITE, COLOR_ACCENT_GREEN)
    tb_f = s10.shapes.add_textbox(Inches(7.05), Inches(2.2), col_w2 - Inches(0.5), Inches(4.4))
    tf_f = tb_f.text_frame
    tf_f.word_wrap = True
    p = tf_f.paragraphs[0]
    p.text = "Upload Bukti Foto Serah Terima Fisik"
    p.font.name = 'Arial'
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_GREEN
    p.space_after = Pt(8)
    add_bullet(tf_f, "Akuntabilitas Nyata:", "Setiap penyerahan fisik barang disertai lampiran foto serah terima langsung di lokasi gudang/ruang kerja.", font_size=10, space_after=5)
    add_bullet(tf_f, "Format Gambar Modern:", "Mendukung kompresi modern WebP, JPG, dan PNG dengan validasi ukuran file maksimal 4MB.", font_size=10, space_after=5)
    add_bullet(tf_f, "Modal Interaktif:", "Tombol 'Lihat Bukti Foto' tersedia di tabel riwayat Pegawai, Subdit, dan Kasubag TU untuk audit visual cepat.", font_size=10, space_after=5)
    add_bullet(tf_f, "Paperless & Hemat:", "Bukti foto TIDAK dicetak ke struk kecil untuk menghemat kertas thermal, melainkan tersimpan di cloud storage.", font_size=10, space_after=5)
    add_bullet(tf_f, "Kontrol Anti-Stocking:", "Audit riwayat pengadaan secara otomatis mendeteksi anomali permintaan berlebih.", font_size=10, space_after=4)

    # =========================================================================
    # SLIDE 11: Minggu Ke-3 — Sprint Hari Ke-4 (Dokumentasi Screenshot)
    # =========================================================================
    s11 = add_base_slide('KEGIATAN MINGGU KE 3 - DOKUMENTASI SPRINT HARI KE-4', 11, font_pt=18)
    add_slide_header(s11, "Dokumentasi Antarmuka: Penyekatan PIC Divisi & Bukti Foto Lapangan", 
                     "Antrean distribusi unit terisolasi serta formulir dan modal pratinjau bukti penyerahan barang")
    
    add_card(s11, Inches(0.65), Inches(1.95), Inches(2.8), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f2/slide_11_pic_7_Picture 9.png'):
        s11.shapes.add_picture('extracted_f2/slide_11_pic_7_Picture 9.png', Inches(1.05), Inches(2.05), Inches(2.0), Inches(4.1))
    tb = s11.shapes.add_textbox(Inches(0.75), Inches(6.25), Inches(2.6), Inches(0.5))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.alignment = PP_ALIGN.CENTER
    p.text = "Antrean Khusus PIC TU"
    p.font.name = 'Arial'
    p.font.size = Pt(10)
    p.font.bold = True
    p.font.color.rgb = COLOR_NAVY
    
    add_card(s11, Inches(3.6), Inches(1.95), Inches(2.8), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f2/slide_11_pic_9_Picture 12.png'):
        s11.shapes.add_picture('extracted_f2/slide_11_pic_9_Picture 12.png', Inches(4.0), Inches(2.05), Inches(2.0), Inches(4.1))
    tb = s11.shapes.add_textbox(Inches(3.7), Inches(6.25), Inches(2.6), Inches(0.5))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.alignment = PP_ALIGN.CENTER
    p.text = "Antrean Khusus PIC SDK"
    p.font.name = 'Arial'
    p.font.size = Pt(10)
    p.font.bold = True
    p.font.color.rgb = COLOR_NAVY

    add_card(s11, Inches(6.55), Inches(1.95), Inches(6.15), Inches(2.35), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f2/slide_11_pic_11_Picture 17.png'):
        s11.shapes.add_picture('extracted_f2/slide_11_pic_11_Picture 17.png', Inches(6.75), Inches(2.05), Inches(2.2), Inches(1.9))
    tb = s11.shapes.add_textbox(Inches(9.1), Inches(2.25), Inches(3.4), Inches(1.6))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "Formulir Upload Bukti PIC"
    p.font.name = 'Arial'
    p.font.size = Pt(11)
    p.font.bold = True
    p.font.color.rgb = COLOR_DARK_TEAL
    p2 = tf.add_paragraph()
    p2.text = "PIC melampirkan foto penyerahan fisik barang secara langsung saat pemohon mengambil barang di gudang."
    p2.font.name = 'Arial'
    p2.font.size = Pt(9.5)
    p2.font.color.rgb = COLOR_TEXT_MAIN
    p2.space_before = Pt(4)

    add_card(s11, Inches(6.55), Inches(4.5), Inches(6.15), Inches(2.35), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f2/slide_11_pic_12_Picture 22.png'):
        s11.shapes.add_picture('extracted_f2/slide_11_pic_12_Picture 22.png', Inches(6.75), Inches(4.6), Inches(2.4), Inches(2.1))
    tb = s11.shapes.add_textbox(Inches(9.3), Inches(4.7), Inches(3.2), Inches(1.8))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "Modal Pratinjau Bukti Foto"
    p.font.name = 'Arial'
    p.font.size = Pt(11)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_GREEN
    p2 = tf.add_paragraph()
    p2.text = "Pimpinan dan staf dapat memverifikasi dokumentasi serah terima lengkap dengan metadata waktu, nama penerima, dan PIC."
    p2.font.name = 'Arial'
    p2.font.size = Pt(9.5)
    p2.font.color.rgb = COLOR_TEXT_MAIN
    p2.space_before = Pt(4)

    # =========================================================================
    # SLIDE 12: Minggu Ke-4 — Cover Minggu Ke-4 / Paparan Utama
    # =========================================================================
    s12 = add_base_slide('KEGIATAN MINGGU KE 4 - PAPARAN APLIKASI & UAT', 12, font_pt=18)
    add_slide_header(s12, "Paparan Aplikasi, Buku Panduan, Alur Sistem, & Hasil Uji Coba", 
                     "Kementerian Perumahan dan Kawasan Permukiman RI • Subbagian Tata Usaha (Tahun Anggaran 2026)")
    
    w4_topics = [
        ("01. Tiga Buku Panduan Pengguna (User Manual Book)",
         "Penerbitan 3 dokumen resmi buku panduan operasional (Role Pegawai, Role Subdirektorat, Role PIC) dengan standar publikasi resmi Kementerian PKP RI.",
         COLOR_ACCENT_TEAL),
        ("02. Tampilan Aplikasi Multi-Role (MacBook View)",
         "Penyelarasan pengalaman pengguna (UI/UX) pada antarmuka staf pemohon perorangan, pemohon kolektif unit kerja, dan staf logistik gudang.",
         COLOR_ACCENT_BLUE),
        ("03. Arsitektur Diagram Alur Sistem Terpadu",
         "Standardisasi 4 diagram alur proses bisnis: alur kerja umum, alur sistem persetujuan Kasubag TU, alur PIC distribusi, dan alur pemohon pegawai.",
         COLOR_DARK_TEAL),
        ("04. Uji Coba Pengguna (User Acceptance Testing - UAT)",
         "Pelaksanaan simulasi operasional bersama perwakilan pengguna nyata lintas unit kerja menggunakan formulir instrumen evaluasi kedinasan.",
         COLOR_ACCENT_GREEN)
    ]
    
    col_w12 = Inches(5.85)
    row_h12 = Inches(2.35)
    
    for idx, (t_title, t_desc, t_color) in enumerate(w4_topics):
        r = idx // 2
        c = idx % 2
        cx = Inches(0.65) if c == 0 else Inches(6.8)
        cy = Inches(2.0) if r == 0 else Inches(4.55)
        
        add_card(s12, cx, cy, col_w12, row_h12, COLOR_WHITE, t_color)
        tb = s12.shapes.add_textbox(cx + Inches(0.3), cy + Inches(0.25), col_w12 - Inches(0.6), row_h12 - Inches(0.5))
        tf = tb.text_frame
        tf.word_wrap = True
        
        p = tf.paragraphs[0]
        p.text = t_title
        p.font.name = 'Arial'
        p.font.size = Pt(13)
        p.font.bold = True
        p.font.color.rgb = t_color
        p.space_after = Pt(6)
        
        p2 = tf.add_paragraph()
        p2.text = t_desc
        p2.font.name = 'Arial'
        p2.font.size = Pt(10.5)
        p2.font.color.rgb = COLOR_TEXT_MAIN

    # =========================================================================
    # SLIDE 13: Minggu Ke-4 — Tiga Buku Panduan Pengguna
    # =========================================================================
    s13 = add_base_slide('KEGIATAN MINGGU KE 4 - TIGA BUKU PANDUAN PENGGUNA', 13, font_pt=18)
    add_slide_header(s13, "Tiga Buku Panduan Pengguna Resmi (User Manual Book)", 
                     "Dokumen resmi panduan operasional kedinasan Kementerian Perumahan dan Kawasan Permukiman RI")
    
    books = [
        ('extracted_f1/slide_2_pic_2_Image 0.jpg', 'Role Pegawai (Pemohon Individu)', 
         'Panduan praktis permohonan ATK staf, katalog persediaan, usulan beli baru, & pemantauan alur persetujuan Kasubag TU.'),
        ('extracted_f1/slide_2_pic_4_Image 1.jpg', 'Role Subdirektorat (Kolektif Unit)', 
         'Panduan komprehensif kelola ATK kolektif tim unit kerja, tracking persetujuan pimpinan, & prosedur serah terima barang.'),
        ('extracted_f1/slide_2_pic_6_Image 2.jpg', 'Role PIC (Pengelola Gudang)', 
         'Standar operasional penyerahan fisik barang ke pemohon, pencatatan mutasi keluar/masuk, & rekap berita acara resmi.')
    ]
    
    for i, (img_p, b_title, b_desc) in enumerate(books):
        cx = Inches(0.8) + i * Inches(4.0)
        add_card(s13, cx, Inches(1.95), Inches(3.65), Inches(4.9), COLOR_WHITE, COLOR_DARK_TEAL)
        if os.path.exists(img_p):
            s13.shapes.add_picture(img_p, cx + Inches(0.55), Inches(2.05), Inches(2.55), Inches(3.55))
        
        tb = s13.shapes.add_textbox(cx + Inches(0.15), Inches(5.7), Inches(3.35), Inches(1.05))
        tf = tb.text_frame
        tf.word_wrap = True
        p = tf.paragraphs[0]
        p.alignment = PP_ALIGN.CENTER
        p.text = b_title
        p.font.name = 'Arial'
        p.font.size = Pt(11)
        p.font.bold = True
        p.font.color.rgb = COLOR_DARK_TEAL
        p.space_after = Pt(3)
        
        p2 = tf.add_paragraph()
        p2.alignment = PP_ALIGN.CENTER
        p2.text = b_desc
        p2.font.name = 'Arial'
        p2.font.size = Pt(8.5)
        p2.font.color.rgb = COLOR_TEXT_MAIN

    # =========================================================================
    # SLIDE 14: Minggu Ke-4 — Tampilan Aplikasi: Pegawai
    # =========================================================================
    s14 = add_base_slide('KEGIATAN MINGGU KE 4 - TAMPILAN APLIKASI: PEGAWAI', 14, font_pt=18)
    add_slide_header(s14, "Tampilan Antarmuka: Role Pegawai (Pemohon Individu)", 
                     "Pengajuan kebutuhan ATK kantor harian/bulanan serta pemantauan status permohonan secara real-time")
    
    add_card(s14, Inches(0.65), Inches(1.95), Inches(8.35), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f1/slide_3_pic_1_Image 0.jpg'):
        s14.shapes.add_picture('extracted_f1/slide_3_pic_1_Image 0.jpg', Inches(0.8), Inches(2.35), Inches(8.05), Inches(3.48))
    
    add_card(s14, Inches(9.2), Inches(1.95), Inches(3.45), Inches(4.9), COLOR_WHITE, COLOR_ACCENT_TEAL)
    tb = s14.shapes.add_textbox(Inches(9.4), Inches(2.15), Inches(3.05), Inches(4.5))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "Fitur Utama Pegawai"
    p.font.name = 'Arial'
    p.font.size = Pt(13)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_TEAL
    p.space_after = Pt(8)
    add_bullet(tf, "E-Katalog Real-Time:", "Cek sisa stok fisik persediaan secara langsung dengan kartu visual barang.", font_size=9.5, space_after=6)
    add_bullet(tf, "Tracking Tiket Permintaan:", "Pantau 4 status permohonan: Menunggu Review, Pengadaan Siap, Selesai Diterima, & Ditolak.", font_size=9.5, space_after=6)
    add_bullet(tf, "Form Usulan Baru:", "Pengajuan pengadaan barang non-katalog bila persediaan kosong.", font_size=9.5, space_after=6)
    add_bullet(tf, "Filter Riwayat Periode:", "Penyaringan data permintaan per bulan dan tahun berjalan.", font_size=9.5, space_after=4)

    # =========================================================================
    # SLIDE 15: Minggu Ke-4 — Tampilan Aplikasi: Subdirektorat
    # =========================================================================
    s15 = add_base_slide('KEGIATAN MINGGU KE 4 - TAMPILAN APLIKASI: SUBDIREKTORAT', 15, font_pt=17.5)
    add_slide_header(s15, "Tampilan Antarmuka: Role Subdirektorat (Pemohon Kolektif)", 
                     "Fasilitas pengajuan ATK skala tim kerja dengan verifikasi berjenjang dan notifikasi barang tersedia")
    
    add_card(s15, Inches(0.65), Inches(1.95), Inches(8.35), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f1/slide_4_pic_1_Image 0.jpg'):
        s15.shapes.add_picture('extracted_f1/slide_4_pic_1_Image 0.jpg', Inches(0.8), Inches(2.35), Inches(8.05), Inches(3.48))
    
    add_card(s15, Inches(9.2), Inches(1.95), Inches(3.45), Inches(4.9), COLOR_WHITE, COLOR_ACCENT_BLUE)
    tb = s15.shapes.add_textbox(Inches(9.4), Inches(2.15), Inches(3.05), Inches(4.5))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "Fitur Utama Subdirektorat"
    p.font.name = 'Arial'
    p.font.size = Pt(13)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_BLUE
    p.space_after = Pt(8)
    add_bullet(tf, "Permintaan Kolektif Unit:", "Mewakili kebutuhan operasional seluruh staf dalam satu divisi kerja.", font_size=9.5, space_after=6)
    add_bullet(tf, "Alert Barang Tersedia:", "Banner hijau interaktif mengabarkan barang pesanan telah dibeli Subbag TU dan masuk katalog.", font_size=9.5, space_after=6)
    add_bullet(tf, "Monitoring Kuota & Rekap:", "Memantau distribusi barang yang telah disetujui untuk unit terkait.", font_size=9.5, space_after=6)
    add_bullet(tf, "Penerbitan BAST Resmi:", "Otomasi pembuatan Berita Acara Serah Terima per divisi kerja.", font_size=9.5, space_after=4)

    # =========================================================================
    # SLIDE 16: Minggu Ke-4 — Tampilan Aplikasi: PIC Distribusi
    # =========================================================================
    s16 = add_base_slide('KEGIATAN MINGGU KE 4 - TAMPILAN APLIKASI: PIC DISTRIBUSI', 16, font_pt=17.5)
    add_slide_header(s16, "Tampilan Antarmuka: Role PIC Distribusi (Pengelola Gudang)", 
                     "Koordinasi, penarikan, dan penyerahan barang per divisi serta pencatatan valuasi persediaan BMN")
    
    add_card(s16, Inches(0.65), Inches(1.95), Inches(8.35), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f1/slide_5_pic_1_Image 0.jpg'):
        s16.shapes.add_picture('extracted_f1/slide_5_pic_1_Image 0.jpg', Inches(0.8), Inches(2.35), Inches(8.05), Inches(3.48))
    
    add_card(s16, Inches(9.2), Inches(1.95), Inches(3.45), Inches(4.9), COLOR_WHITE, COLOR_DARK_TEAL)
    tb = s16.shapes.add_textbox(Inches(9.4), Inches(2.15), Inches(3.05), Inches(4.5))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "Fitur Utama PIC Logistik"
    p.font.name = 'Arial'
    p.font.size = Pt(13)
    p.font.bold = True
    p.font.color.rgb = COLOR_DARK_TEAL
    p.space_after = Pt(8)
    add_bullet(tf, "Antrean Siap Serah:", "Daftar permohonan yang telah disetujui Kasubag TU siap diserahkan ke staf.", font_size=9.5, space_after=6)
    add_bullet(tf, "Pencatatan Keluar:", "Fitur catat pengeluaran gudang real-time memotong stok otomatis.", font_size=9.5, space_after=6)
    add_bullet(tf, "Valuasi Persediaan (Rp):", "Monitoring total nilai rupiah stok fisik gudang berbasis harga satuan faktur.", font_size=9.5, space_after=6)
    add_bullet(tf, "Dokumentasi Foto Lapangan:", "Kewajiban melampirkan foto serah terima saat penyerahan fisik barang.", font_size=9.5, space_after=4)

    # =========================================================================
    # SLIDE 17: Minggu Ke-4 — Alur Kerja 6 Tahapan Terpadu
    # =========================================================================
    s17 = add_base_slide('KEGIATAN MINGGU KE 4 - ALUR KERJA TERPADU SISTEM', 17, font_pt=18)
    add_slide_header(s17, "Alur Kerja: Permintaan Sampai Serah Terima Barang", 
                     "Rantai proses operasional standar pengelolaan ATK dari pembuatan akun hingga penerbitan Berita Acara")
    
    steps = [
        ("1. Akun & Login", "Pemohon masuk dengan akun kedinasan terintegrasi NIP yang didaftarkan resmi.", COLOR_ACCENT_TEAL),
        ("2. Permintaan", "Ajukan kebutuhan ATK dari persediaan stok gudang atau usul pengadaan beli baru.", COLOR_ACCENT_BLUE),
        ("3. Persetujuan", "Kasubag TU meninjau permohonan: menyetujui, menyetujui parsial, atau menolak.", COLOR_ACCENT_AMBER),
        ("4. SPB & Pembelian", "Penerbitan Surat Permintaan Barang (SPB), proses belanja rekanan, & auto-katalog.", COLOR_DARK_TEAL),
        ("5. Distribusi", "PIC unit menyerahkan fisik barang ke pemohon disertai unggah foto bukti lapangan.", COLOR_ACCENT_GREEN),
        ("6. STB & TTD", "Cetak Surat Tanda Terima Barang (STB), tanda tangan KTU dan Kasubdit resmi.", COLOR_NAVY)
    ]
    
    col_w17 = Inches(3.85)
    row_h17 = Inches(1.85)
    
    for idx, (s_title, s_desc, s_color) in enumerate(steps):
        r = idx // 3
        c = idx % 3
        cx = Inches(0.65) + c * Inches(4.1)
        cy = Inches(2.0) + r * Inches(2.0)
        
        add_card(s17, cx, cy, col_w17, row_h17, COLOR_WHITE, s_color)
        tb = s17.shapes.add_textbox(cx + Inches(0.2), cy + Inches(0.15), col_w17 - Inches(0.4), row_h17 - Inches(0.3))
        tf = tb.text_frame
        tf.word_wrap = True
        
        p = tf.paragraphs[0]
        p.text = s_title
        p.font.name = 'Arial'
        p.font.size = Pt(12)
        p.font.bold = True
        p.font.color.rgb = s_color
        p.space_after = Pt(4)
        
        p2 = tf.add_paragraph()
        p2.text = s_desc
        p2.font.name = 'Arial'
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = COLOR_TEXT_MAIN

    # Bottom Alert Card: Alur Penolakan
    add_card(s17, Inches(0.65), Inches(6.05), Inches(12.05), Inches(0.85), COLOR_LIGHT_AMBER, COLOR_ACCENT_AMBER)
    tb_alert = s17.shapes.add_textbox(Inches(0.85), Inches(6.15), Inches(11.65), Inches(0.65))
    tf_alert = tb_alert.text_frame
    tf_alert.word_wrap = True
    p = tf_alert.paragraphs[0]
    r1 = p.add_run()
    r1.text = "Mekanisme Penolakan & Revisi: "
    r1.font.bold = True
    r1.font.size = Pt(10.5)
    r1.font.color.rgb = COLOR_ACCENT_AMBER
    r2 = p.add_run()
    r2.text = "Jika permohonan ditolak, Kasubag TU wajib menyertakan catatan alasan penolakan. Sistem mengirimkan notifikasi instan kepada pemohon untuk merevisi draf dan mengajukan ulang tanpa membuat tiket dari nol."
    r2.font.size = Pt(10)
    r2.font.color.rgb = COLOR_TEXT_MAIN

    # =========================================================================
    # SLIDE 18: Minggu Ke-4 — Alur Sistem Diagram
    # =========================================================================
    s18 = add_base_slide('KEGIATAN MINGGU KE 4 - ALUR SISTEM & PERSETUJUAN KASUBAG TU', 18, font_pt=16)
    add_slide_header(s18, "Alur Sistem: Tata Kelola Pengajuan, Verifikasi, & Pengadaan", 
                     "Diagram alir menyeluruh dari registrasi akun, verifikasi bertingkat Kasubag TU, hingga serah terima")
    
    add_card(s18, Inches(0.65), Inches(1.95), Inches(4.35), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f1/slide_7_pic_1_Image 0.png'):
        s18.shapes.add_picture('extracted_f1/slide_7_pic_1_Image 0.png', Inches(0.8), Inches(2.05), Inches(4.05), Inches(4.7))
    
    add_card(s18, Inches(5.2), Inches(1.95), Inches(7.5), Inches(4.9), COLOR_WHITE, COLOR_DARK_TEAL)
    tb = s18.shapes.add_textbox(Inches(5.45), Inches(2.15), Inches(7.0), Inches(4.5))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "Dekonstruksi Logika Alur Sistem Utama"
    p.font.name = 'Arial'
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = COLOR_DARK_TEAL
    p.space_after = Pt(8)
    add_bullet(tf, "1. Inisiasi Akun:", "Akun dibuatkan atau diverifikasi oleh Kasubag TU sebelum pemohon login ke sistem.", font_size=10, space_after=5)
    add_bullet(tf, "2. Permintaan Pengadaan:", "Pemohon mengajukan kebutuhan ATK pengadaan yang belum tersedia di katalog gudang.", font_size=10, space_after=5)
    add_bullet(tf, "3. Decision Point Kasubag:", "Jika DITOLAK: Kasubag memberi alasan, pemohon revisi. Jika DISETUJUI: sistem terbitkan SPB.", font_size=10, space_after=5)
    add_bullet(tf, "4. Proses Belanja & Katalog:", "Pembelian dilakukan secara offline TU, lalu barang otomatis teregistrasi ke e-katalog persediaan.", font_size=10, space_after=5)
    add_bullet(tf, "5. Pengajuan Pengambilan:", "Pemohon membuat tiket pengajuan baru atas barang yang kini telah siap di katalog.", font_size=10, space_after=5)
    add_bullet(tf, "6. Distribusi & Foto Bukti:", "PIC menyerahkan fisik barang dan mengunggah foto bukti serah terima secara langsung.", font_size=10, space_after=5)
    add_bullet(tf, "7. Validasi STB & KTU:", "Cetak Berita Acara STB dan pengajuan tanda tangan basah/elektronik kepada KTU.", font_size=10, space_after=4)

    # =========================================================================
    # SLIDE 19: Minggu Ke-4 — Alur PIC Diagram
    # =========================================================================
    s19 = add_base_slide('KEGIATAN MINGGU KE 4 - ALUR OPERASIONAL PIC DISTRIBUSI', 19, font_pt=16.5)
    add_slide_header(s19, "Alur Kerja PIC: Distribusi Gudang & Penyerahan Fisik", 
                     "Diagram alir tugas operasional PIC logistik dari notifikasi ketersediaan hingga tanda tangan pejabat")
    
    add_card(s19, Inches(0.65), Inches(1.95), Inches(3.2), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f1/slide_8_pic_1_Image 0.png'):
        s19.shapes.add_picture('extracted_f1/slide_8_pic_1_Image 0.png', Inches(0.85), Inches(2.05), Inches(2.8), Inches(4.7))
    
    add_card(s19, Inches(4.05), Inches(1.95), Inches(8.65), Inches(4.9), COLOR_WHITE, COLOR_ACCENT_TEAL)
    tb = s19.shapes.add_textbox(Inches(4.35), Inches(2.15), Inches(8.05), Inches(4.5))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "Tanggung Jawab Operasional PIC Distribusi"
    p.font.name = 'Arial'
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_TEAL
    p.space_after = Pt(8)
    add_bullet(tf, "1. Otomasi Antrean Serah:", "PIC hanya menerima notifikasi tiket barang yang telah lolos verifikasi Kasubag TU.", font_size=10, space_after=5)
    add_bullet(tf, "2. Pemeriksaan Fisik Barang:", "PIC memastikan kecocokan merk, tipe, dan kuantitas barang fisik dengan faktur belanja.", font_size=10, space_after=5)
    add_bullet(tf, "3. Verifikasi Identitas Pemohon:", "Penyerahan dilakukan langsung kepada pemohon atau perwakilan resmi unit kerja.", font_size=10, space_after=5)
    add_bullet(tf, "4. Dokumentasi Digital Lapangan:", "PIC wajib mengambil dan mengunggah foto serah terima sebagai lampiran sistem yang sah.", font_size=10, space_after=5)
    add_bullet(tf, "5. Penerbitan Surat Tanda Terima (STB):", "Sistem meng-generate dokumen STB lengkap dengan nomor registrasi BMN dan rincian item.", font_size=10, space_after=5)
    add_bullet(tf, "6. Pengesahan Pimpinan:", "PIC mengajukan tanda tangan pengesahan kepada KTU dan Kasubdit terkait untuk arsip audit.", font_size=10, space_after=4)

    # =========================================================================
    # SLIDE 20: Minggu Ke-4 — Alur Pegawai Diagram
    # =========================================================================
    s20 = add_base_slide('KEGIATAN MINGGU KE 4 - ALUR PEGAWAI INDIVIDU', 20, font_pt=18)
    add_slide_header(s20, "Alur Pemohon Pegawai: Registrasi Mandiri s/d Penerimaan ATK", 
                     "Diagram alir interaksi staf pemohon individu dalam mengajukan kebutuhan perlengkapan dinas")
    
    add_card(s20, Inches(0.65), Inches(1.95), Inches(5.4), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f1/slide_9_pic_1_Image 0.png'):
        s20.shapes.add_picture('extracted_f1/slide_9_pic_1_Image 0.png', Inches(0.8), Inches(2.05), Inches(5.1), Inches(4.7))
    
    add_card(s20, Inches(6.25), Inches(1.95), Inches(6.45), Inches(4.9), COLOR_WHITE, COLOR_ACCENT_BLUE)
    tb = s20.shapes.add_textbox(Inches(6.5), Inches(2.15), Inches(5.95), Inches(4.5))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.text = "Panduan Tahapan Pengajuan Staf ASN"
    p.font.name = 'Arial'
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_BLUE
    p.space_after = Pt(8)
    add_bullet(tf, "1. Registrasi Akun Mandiri:", "Staf mendaftar akun baru dengan memasukkan NIP dinas dan memilih role Pegawai.", font_size=10, space_after=5)
    add_bullet(tf, "2. Eksplorasi E-Katalog:", "Memeriksa stok barang persediaan. Jika ada stok -> langsung pesan; jika kosong -> ajukan usulan pengadaan.", font_size=10, space_after=5)
    add_bullet(tf, "3. Verifikasi Kasubag TU:", "Permohonan masuk ke antrean pimpinan untuk disetujui atau ditolak dengan catatan.", font_size=10, space_after=5)
    add_bullet(tf, "4. Pembelian & Auto-Notifikasi:", "Subbag TU memproses pembelian rekanan dan sistem mengirim notifikasi bahwa barang sudah tiba.", font_size=10, space_after=5)
    add_bullet(tf, "5. Pengambilan Fisik ke PIC TU:", "Pegawai mendatangi PIC Logistik untuk serah terima fisik barang dan pengambilan foto bukti.", font_size=10, space_after=5)
    add_bullet(tf, "6. Selesai & Arsip:", "Status tiket otomatis berubah menjadi 'Selesai Diterima' dan tersimpan di riwayat pribadi.", font_size=10, space_after=4)

    # =========================================================================
    # SLIDE 21: Minggu Ke-4 — Uji Coba Pengguna (UAT)
    # =========================================================================
    s21 = add_base_slide('KEGIATAN MINGGU KE 4 - UJI COBA PENGGUNA (UAT)', 21, font_pt=18)
    add_slide_header(s21, "Uji Coba Pengguna (User Acceptance Testing - UAT)", 
                     "Evaluasi dan umpan balik pengguna terkait kelayakan operasional sistem Aplikasi ATEKA di lingkungan instansi")
    
    add_card(s21, Inches(0.65), Inches(1.95), Inches(4.2), Inches(4.9), COLOR_WHITE, COLOR_CARD_BORDER)
    if os.path.exists('extracted_f1/slide_10_pic_3_Picture 15.png'):
        s21.shapes.add_picture('extracted_f1/slide_10_pic_3_Picture 15.png', Inches(0.85), Inches(2.05), Inches(3.8), Inches(4.3))
    tb = s21.shapes.add_textbox(Inches(0.85), Inches(6.4), Inches(3.8), Inches(0.4))
    tf = tb.text_frame
    tf.word_wrap = True
    p = tf.paragraphs[0]
    p.alignment = PP_ALIGN.CENTER
    p.text = "Kuesioner Uji Coba Pengguna (Google Forms)"
    p.font.name = 'Arial'
    p.font.size = Pt(9.5)
    p.font.bold = True
    p.font.color.rgb = COLOR_NAVY

    add_card(s21, Inches(5.05), Inches(1.95), Inches(7.65), Inches(4.9), COLOR_WHITE, COLOR_ACCENT_GREEN)
    tb_u = s21.shapes.add_textbox(Inches(5.3), Inches(2.15), Inches(7.15), Inches(4.5))
    tf_u = tb_u.text_frame
    tf_u.word_wrap = True
    p = tf_u.paragraphs[0]
    p.text = "Hasil Evaluasi & Kesiapan Rilis Sistem ATEKA"
    p.font.name = 'Arial'
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_GREEN
    p.space_after = Pt(8)
    add_bullet(tf_u, "Metodologi Pengujian Lapangan:", "Simulasi menyeluruh melibatkan 30+ responden perwakilan Pegawai, Subdit SDK, BM, PJF, serta pengelola logistik Subbag TU Dit. Bina Teknik PKP.", font_size=10, space_after=5)
    add_bullet(tf_u, "Parameter Uji Coba:", "Pengujian mencakup kemudahan registrasi NIP, kecepatan pengajuan barang e-katalog, akurasi pemisahan antrean pengadaan baru, dan kejelasan notifikasi lonceng.", font_size=10, space_after=5)
    add_bullet(tf_u, "Umpan Balik Positif:", "Pengguna mengapresiasi transparansi alur tiket, ketiadaan proses manual yang berbelit, serta tersedianya bukti foto serah terima yang menjamin akuntabilitas BMN.", font_size=10, space_after=5)
    add_bullet(tf_u, "Penyempurnaan Berkelanjutan:", "Masukan pengguna terkait penyesuaian ukuran teks struk dan penataan format nama gelar pejabat telah diakomodasi penuh pada rilis versi stabil.", font_size=10, space_after=5)
    add_bullet(tf_u, "Kesiapan Operasional:", "Sistem ATEKA dinyatakan SIAP DIGUNAKAN SECARA PENUH untuk pengelolaan persediaan dan pengadaan ATK Tahun Anggaran 2026.", font_size=10, space_after=4)

    # =========================================================================
    # SLIDE 22: Slide Penutup (Terima Kasih)
    # =========================================================================
    s22 = add_base_slide('KEGIATAN MINGGU KE 3 & 4 - SELESAI & TANYA JAWAB', 22, font_pt=18)
    
    add_card(s22, Inches(2.0), Inches(1.8), Inches(9.333), Inches(5.1), COLOR_WHITE, COLOR_DARK_TEAL)
    
    if os.path.exists('extracted_f2/slide_12_pic_1_Picture 2.jpg'):
        s22.shapes.add_picture('extracted_f2/slide_12_pic_1_Picture 2.jpg', Inches(5.9), Inches(2.1), Inches(1.5), Inches(1.5))
    
    tb_c = s22.shapes.add_textbox(Inches(2.5), Inches(3.75), Inches(8.333), Inches(2.8))
    tf_c = tb_c.text_frame
    tf_c.word_wrap = True
    
    p = tf_c.paragraphs[0]
    p.alignment = PP_ALIGN.CENTER
    p.text = "TERIMA KASIH"
    p.font.name = 'Arial'
    p.font.size = Pt(28)
    p.font.bold = True
    p.font.color.rgb = COLOR_DARK_TEAL
    
    p = tf_c.add_paragraph()
    p.alignment = PP_ALIGN.CENTER
    p.text = "Sesi Diskusi, Masukan & Tanya Jawab"
    p.font.name = 'Arial'
    p.font.size = Pt(14)
    p.font.bold = True
    p.font.color.rgb = COLOR_NAVY
    p.space_after = Pt(8)
    
    p = tf_c.add_paragraph()
    p.alignment = PP_ALIGN.CENTER
    p.text = "APLIKASI TATA KELOLA PERSEDIAAN & PENGADAAN ATK (ATEKA)"
    p.font.name = 'Arial'
    p.font.size = Pt(12)
    p.font.bold = True
    p.font.color.rgb = COLOR_ACCENT_AMBER
    
    p = tf_c.add_paragraph()
    p.alignment = PP_ALIGN.CENTER
    p.text = "Direktorat Bina Teknik Perumahan dan Kawasan Permukiman • Subbagian Tata Usaha\nKementerian Perumahan dan Kawasan Permukiman Republik Indonesia • TA 2026"
    p.font.name = 'Arial'
    p.font.size = Pt(10)
    p.font.color.rgb = COLOR_TEXT_MUTED
    p.space_before = Pt(4)

    out_path = 'Presentasi_Gabungan_ATEKA_Minggu_3_dan_4.pptx'
    prs.save(out_path)
    print(f"Presentation saved successfully to: {out_path}")

if __name__ == '__main__':
    build_presentation()
