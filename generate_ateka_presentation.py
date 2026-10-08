import os
import sys
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.dml.color import RGBColor
from pptx.enum.shapes import MSO_SHAPE

def create_presentation():
    prs = Presentation()
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_layout = prs.slide_layouts[6]

    # Palet Warna Resmi ATEKA & Kementerian PKP
    NAVY = RGBColor(20, 75, 89)        # #144B59 (Brand Utama ATEKA)
    NAVY_DARK = RGBColor(12, 45, 54)   # #0C2D36
    BLUE = RGBColor(2, 132, 199)       # #0284C7 (Accent Sky)
    BLUE_LIGHT = RGBColor(224, 242, 254) # #E0F2FE
    BG_LIGHT = RGBColor(248, 250, 252) # #F8FAFC
    WHITE = RGBColor(255, 255, 255)
    CARD_BG = RGBColor(255, 255, 255)
    BORDER_COLOR = RGBColor(226, 232, 240) # #E2E8F0
    TEXT_DARK = RGBColor(15, 23, 42)    # #0F172A
    TEXT_MUTED = RGBColor(100, 116, 139) # #64748B
    GREEN = RGBColor(16, 185, 129)     # #10B981
    GREEN_LIGHT = RGBColor(209, 250, 229)
    AMBER = RGBColor(245, 158, 11)     # #F59E0B
    AMBER_LIGHT = RGBColor(254, 243, 199)
    RED = RGBColor(239, 68, 68)        # #EF4444
    RED_LIGHT = RGBColor(254, 226, 226)

    LOGO_PATH = os.path.abspath('public/hope-ui/assets/images/auth/kemenkp.jpeg')
    DIAGRAM_ALUR = os.path.abspath('public/img/navigasi/alur_proses_navigasi_lintas_role.png')

    def apply_bg(slide, color=BG_LIGHT):
        bg = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, prs.slide_width, prs.slide_height)
        bg.fill.solid()
        bg.fill.fore_color.rgb = color
        bg.line.fill.background()
        return bg

    def add_header(slide, title, category_badge, subtitle=None):
        # Top banner accent bar
        bar = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0), Inches(0), prs.slide_width, Inches(0.12))
        bar.fill.solid()
        bar.fill.fore_color.rgb = NAVY
        bar.line.fill.background()

        # Category Badge
        badge = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(0.35), Inches(3.8), Inches(0.36))
        badge.fill.solid()
        badge.fill.fore_color.rgb = BLUE_LIGHT
        badge.line.color.rgb = BLUE
        badge.line.width = Pt(1)
        tf_b = badge.text_frame
        tf_b.word_wrap = True
        tf_b.margin_left = Inches(0.1)
        tf_b.margin_top = Inches(0.04)
        p_b = tf_b.paragraphs[0]
        p_b.text = category_badge.upper()
        p_b.font.size = Pt(9.5)
        p_b.font.bold = True
        p_b.font.color.rgb = BLUE
        p_b.alignment = PP_ALIGN.CENTER

        # Title Box
        tb = slide.shapes.add_textbox(Inches(0.75), Inches(0.75), Inches(10.5), Inches(0.65))
        tf = tb.text_frame
        tf.word_wrap = True
        tf.margin_left = tf.margin_top = tf.margin_right = tf.margin_bottom = 0
        p = tf.paragraphs[0]
        p.text = title
        p.font.size = Pt(22)
        p.font.bold = True
        p.font.color.rgb = TEXT_DARK
        p.font.name = "Arial"

        if subtitle:
            p2 = tf.add_paragraph()
            p2.text = subtitle
            p2.font.size = Pt(11.5)
            p2.font.color.rgb = TEXT_MUTED
            p2.font.name = "Arial"
            p2.space_before = Pt(3)

        # Ministry Logo on top right
        if os.path.exists(LOGO_PATH):
            slide.shapes.add_picture(LOGO_PATH, Inches(12.0), Inches(0.35), Inches(0.65), Inches(0.65))

    def add_footer(slide, current_page, total_pages=11):
        # Divider line
        line = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(0.8), Inches(7.0), Inches(11.733), Pt(1))
        line.fill.solid()
        line.fill.fore_color.rgb = BORDER_COLOR
        line.line.fill.background()

        # Footer Left
        tb_l = slide.shapes.add_textbox(Inches(0.8), Inches(7.05), Inches(9.0), Inches(0.3))
        tf_l = tb_l.text_frame
        tf_l.margin_left = tf_l.margin_top = 0
        p_l = tf_l.paragraphs[0]
        p_l.text = "ATEKA PKP • Inventori & Pengadaan ATK • Kementerian Perumahan dan Kawasan Permukiman RI"
        p_l.font.size = Pt(9)
        p_l.font.color.rgb = TEXT_MUTED

        # Footer Right (Page Number)
        tb_r = slide.shapes.add_textbox(Inches(10.5), Inches(7.05), Inches(2.0), Inches(0.3))
        tf_r = tb_r.text_frame
        tf_r.margin_left = tf_r.margin_top = 0
        p_r = tf_r.paragraphs[0]
        p_r.text = f"Halaman {current_page} dari {total_pages}"
        p_r.font.size = Pt(9)
        p_r.font.bold = True
        p_r.font.color.rgb = TEXT_MUTED
        p_r.alignment = PP_ALIGN.RIGHT

    def create_card(slide, left, top, width, height, title, bg_color=CARD_BG, border_color=BORDER_COLOR, title_color=NAVY):
        card = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, left, top, width, height)
        card.fill.solid()
        card.fill.fore_color.rgb = bg_color
        card.line.color.rgb = border_color
        card.line.width = Pt(1.2)
        
        # Inner header
        tb = slide.shapes.add_textbox(left + Inches(0.2), top + Inches(0.18), width - Inches(0.4), Inches(0.45))
        tf = tb.text_frame
        tf.word_wrap = True
        tf.margin_left = tf.margin_top = tf.margin_bottom = tf.margin_right = 0
        p = tf.paragraphs[0]
        p.text = title
        p.font.size = Pt(13)
        p.font.bold = True
        p.font.color.rgb = title_color
        p.font.name = "Arial"
        return card

    # ==========================================
    # SLIDE 1: COVER SLIDE
    # ==========================================
    s1 = prs.slides.add_slide(blank_layout)
    bg1 = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, prs.slide_width, prs.slide_height)
    bg1.fill.solid()
    bg1.fill.fore_color.rgb = NAVY_DARK
    bg1.line.fill.background()

    # Decorative geometric shape
    deco = s1.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(8.5), Inches(0), Inches(4.833), prs.slide_height)
    deco.fill.solid()
    deco.fill.fore_color.rgb = NAVY
    deco.line.fill.background()

    # Logo on cover
    if os.path.exists(LOGO_PATH):
        s1.shapes.add_picture(LOGO_PATH, Inches(1.2), Inches(1.2), Inches(1.3), Inches(1.3))

    # Badge Instansi
    b_cov = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(1.2), Inches(2.75), Inches(4.8), Inches(0.38))
    b_cov.fill.solid()
    b_cov.fill.fore_color.rgb = BLUE
    b_cov.line.fill.background()
    p = b_cov.text_frame.paragraphs[0]
    p.text = "KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN RI"
    p.font.size = Pt(9.5)
    p.font.bold = True
    p.font.color.rgb = WHITE
    p.alignment = PP_ALIGN.CENTER

    # Main Title
    tb_cov = s1.shapes.add_textbox(Inches(1.2), Inches(3.25), Inches(7.5), Inches(2.2))
    tf_cov = tb_cov.text_frame
    tf_cov.word_wrap = True
    p_t1 = tf_cov.paragraphs[0]
    p_t1.text = "APLIKASI ATEKA"
    p_t1.font.size = Pt(36)
    p_t1.font.bold = True
    p_t1.font.color.rgb = WHITE
    p_t1.font.name = "Arial"

    p_t2 = tf_cov.add_paragraph()
    p_t2.text = "Laporan Perkembangan & Pembaruan Fitur Aplikasi"
    p_t2.font.size = Pt(20)
    p_t2.font.color.rgb = RGBColor(186, 230, 253)
    p_t2.space_before = Pt(6)

    p_t3 = tf_cov.add_paragraph()
    p_t3.text = "Sprint Intensif 4 Hari Kerja: Senin s/d Kamis (05 – 08 Oktober 2026)"
    p_t3.font.size = Pt(13)
    p_t3.font.color.rgb = RGBColor(148, 163, 184)
    p_t3.space_before = Pt(8)

    # Subbagian Info Box
    card_info = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(1.2), Inches(5.6), Inches(6.5), Inches(1.1))
    card_info.fill.solid()
    card_info.fill.fore_color.rgb = RGBColor(15, 23, 42)
    card_info.line.color.rgb = RGBColor(56, 189, 248)
    card_info.line.width = Pt(1)
    tf_ci = card_info.text_frame
    tf_ci.margin_left = Inches(0.2)
    tf_ci.margin_top = Inches(0.15)
    p_ci1 = tf_ci.paragraphs[0]
    p_ci1.text = "Unit Kerja : Direktorat Bina Teknik Perumahan dan Kawasan Permukiman"
    p_ci1.font.size = Pt(10.5)
    p_ci1.font.bold = True
    p_ci1.font.color.rgb = WHITE
    p_ci2 = tf_ci.add_paragraph()
    p_ci2.text = "Pengelola  : Subbagian Tata Usaha (Kasubag TU) & Tim Pengembang Sistem"
    p_ci2.font.size = Pt(10)
    p_ci2.font.color.rgb = RGBColor(148, 163, 184)

    # Right side Highlights on Cover
    tb_hl = s1.shapes.add_textbox(Inches(9.0), Inches(1.3), Inches(3.8), Inches(5.0))
    tf_hl = tb_hl.text_frame
    tf_hl.word_wrap = True
    p_h = tf_hl.paragraphs[0]
    p_h.text = "POKOK PEMBARUAN"
    p_h.font.size = Pt(13)
    p_h.font.bold = True
    p_h.font.color.rgb = RGBColor(56, 189, 248)

    highlights = [
        ("01", "Pemisahan Alur Pengadaan vs Stok", "Usulan beli baru non-katalog & verifikasi offline."),
        ("02", "Tata Kelola Akun & NIP ASN", "Integrasi NIP, kelola PIC 4 unit, & reset password mandiri."),
        ("03", "Notifikasi Lonceng & Berita Acara", "Notifikasi navbar real-time & gelar resmi ASN."),
        ("04", "Penyekatan Multi-Divisi PIC", "PIC distribusi per unit (SDK, BM, KSPST, TU)."),
        ("05", "Upload Bukti Foto Fisik", "Dokumentasi digital penyerahan barang bukti riil."),
        ("06", "Kontrol Anti-Stocking ATK", "Audit riwayat pengadaan & pencegahan penimbunan barang.")
    ]
    for num, title_h, desc_h in highlights:
        p_item = tf_hl.add_paragraph()
        p_item.text = f"[{num}] {title_h}"
        p_item.font.size = Pt(11)
        p_item.font.bold = True
        p_item.font.color.rgb = WHITE
        p_item.space_before = Pt(8)
        
        p_desc = tf_hl.add_paragraph()
        p_desc.text = desc_h
        p_desc.font.size = Pt(9.5)
        p_desc.font.color.rgb = RGBColor(203, 213, 225)

    # ==========================================
    # SLIDE 2: ROADMAP & TIMELINE 4 HARI
    # ==========================================
    s2 = prs.slides.add_slide(blank_layout)
    apply_bg(s2)
    add_header(s2, "Roadmap Transformasi & Sprint 4 Hari", "IKHTISAR EKSEKUTIF", "Perkembangan arsitektur dan kapabilitas sistem ATEKA dari tanggal 05 hingga 08 Oktober 2026")
    add_footer(s2, 2)

    days = [
        {
            "day": "HARI 1 • SENIN",
            "date": "05 Oktober 2026",
            "title": "Fondasi Usulan Pengadaan & Rekapitulasi",
            "color": NAVY,
            "badge_color": BLUE_LIGHT,
            "badge_text_color": BLUE,
            "items": [
                "Form Khusus Pengadaan (Beli Baru) untuk barang habis/non-katalog.",
                "Pemisahan Antrean Kasubag TU: Tab Pengadaan vs Persediaan Stok.",
                "Persetujuan Parsial & Input Faktur Pembelian Offline TU.",
                "Modul Rekapitulasi & Cetak Berita Acara resmi format Dit. Bina Teknik PKP."
            ]
        },
        {
            "day": "HARI 2 • SELASA",
            "date": "06 Oktober 2026",
            "title": "Standardisasi NIP & Tata Kelola Pengguna",
            "color": NAVY,
            "badge_color": AMBER_LIGHT,
            "badge_text_color": RGBColor(180, 83, 9),
            "items": [
                "Integrasi NIP pada form registrasi dan seluruh profil ASN.",
                "Master Pengguna Mandiri: Kasubag TU dapat menambah PIC & Pegawai.",
                "Self-Service Reset Password berbasis verifikasi NIP dan Email.",
                "Modernisasi UI Navigasi & Modal Interaktif SweetAlert2."
            ]
        },
        {
            "day": "HARI 3 • RABU",
            "date": "07 Oktober 2026",
            "title": "Notifikasi Real-Time & Legalitas Dokumen",
            "color": NAVY,
            "badge_color": GREEN_LIGHT,
            "badge_text_color": RGBColor(4, 120, 87),
            "items": [
                "Sistem Notifikasi Lonceng Terpusat di Navbar dengan unread counter.",
                "Penambahan Gelar Akademik (name_with_title) untuk keabsahan surat.",
                "Sinkronisasi Otomatis Stok Real-Time pada CRUD Master Produk.",
                "Penyelarasan Struk Bukti Pengeluaran Barang."
            ]
        },
        {
            "day": "HARI 4 • KAMIS",
            "date": "08 Oktober 2026 (HARI INI)",
            "title": "Penyekatan Divisi, Bukti Foto & Anti-Stocking",
            "color": NAVY_DARK,
            "badge_color": RGBColor(254, 226, 226),
            "badge_text_color": RED,
            "items": [
                "Penyekatan PIC Distribusi: SDK, BM, KSPST, & TU berdiri independen.",
                "Hak Distribusi dicabut dari Kasubag TU (fokus Approver/Manajerial).",
                "Fitur Upload Bukti Foto Serah Terima Fisik (WebP/JPG/PNG).",
                "Audit Riwayat Pengadaan & Kontrol Anti-Stocking Barang ATK."
            ]
        }
    ]

    card_w = Inches(2.78)
    card_h = Inches(4.9)
    gap = Inches(0.18)
    start_x = Inches(0.8)
    start_y = Inches(1.8)

    for i, d in enumerate(days):
        cx = start_x + i * (card_w + gap)
        # Card Background
        card = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, cx, start_y, card_w, card_h)
        card.fill.solid()
        card.fill.fore_color.rgb = WHITE
        card.line.color.rgb = BORDER_COLOR
        card.line.width = Pt(1.5 if i == 3 else 1)

        # Top tag
        tag = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, cx + Inches(0.15), start_y + Inches(0.15), card_w - Inches(0.3), Inches(0.32))
        tag.fill.solid()
        tag.fill.fore_color.rgb = d["badge_color"]
        tag.line.fill.background()
        p = tag.text_frame.paragraphs[0]
        p.text = d["day"]
        p.font.size = Pt(8.5)
        p.font.bold = True
        p.font.color.rgb = d["badge_text_color"]
        p.alignment = PP_ALIGN.CENTER

        # Date & Title
        tb = s2.shapes.add_textbox(cx + Inches(0.15), start_y + Inches(0.55), card_w - Inches(0.3), Inches(0.95))
        tf = tb.text_frame
        tf.word_wrap = True
        tf.margin_left = tf.margin_top = tf.margin_right = 0
        p_dt = tf.paragraphs[0]
        p_dt.text = d["date"]
        p_dt.font.size = Pt(9.5)
        p_dt.font.color.rgb = TEXT_MUTED

        p_t = tf.add_paragraph()
        p_t.text = d["title"]
        p_t.font.size = Pt(11.5)
        p_t.font.bold = True
        p_t.font.color.rgb = TEXT_DARK
        p_t.space_before = Pt(3)

        # Divider
        div = s2.shapes.add_shape(MSO_SHAPE.RECTANGLE, cx + Inches(0.15), start_y + Inches(1.55), card_w - Inches(0.3), Pt(1))
        div.fill.solid()
        div.fill.fore_color.rgb = BORDER_COLOR
        div.line.fill.background()

        # Items
        tb_i = s2.shapes.add_textbox(cx + Inches(0.15), start_y + Inches(1.65), card_w - Inches(0.3), Inches(3.1))
        tf_i = tb_i.text_frame
        tf_i.word_wrap = True
        tf_i.margin_left = tf_i.margin_top = tf_i.margin_right = 0
        for idx, item in enumerate(d["items"]):
            p_it = tf_i.paragraphs[0] if idx == 0 else tf_i.add_paragraph()
            p_it.text = f"• {item}"
            p_it.font.size = Pt(10)
            p_it.font.color.rgb = RGBColor(51, 65, 85)
            p_it.space_before = Pt(8 if idx > 0 else 0)

    # ==========================================
    # SLIDE 3: HARI 1 (SENIN) - PENGADAAN & REKAP
    # ==========================================
    s3 = prs.slides.add_slide(blank_layout)
    apply_bg(s3)
    add_header(s3, "Hari 1 (Senin, 05 Okt 2026): Fondasi Pengadaan & Rekapitulasi", "SPRINT HARI KE-1", "Membuka alur pengadaan barang baru serta memisahkan antrean verifikasi Kasubag TU")
    add_footer(s3, 3)

    # 3 Cards Layout
    c1 = create_card(s3, Inches(0.8), Inches(1.8), Inches(3.7), Inches(4.9), "Form Usulan Pengadaan (Beli Baru)", WHITE, BORDER_COLOR)
    tb = s3.shapes.add_textbox(Inches(1.0), Inches(2.4), Inches(3.3), Inches(4.1))
    tf = tb.text_frame
    tf.word_wrap = True
    tf.margin_left = tf.margin_top = 0
    bullets1 = [
        ("Latar Belakang:", "Sebelumnya sistem hanya melayani permintaan barang yang stoknya ada di gudang."),
        ("Formulir Khusus:", "Pegawai & Subdit dapat mengusulkan barang baru / habis di katalog."),
        ("Parameter Detail:", "Input nama barang, spesifikasi teknis, estimasi harga satuan, dan kuantitas permintaan."),
        ("Kolektif & Individu:", "Mendukung pengajuan Subdit (kolektif) maupun Pegawai (individu).")
    ]
    for i, (b_title, b_desc) in enumerate(bullets1):
        p = tf.paragraphs[0] if i == 0 else tf.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(10.5)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(10 if i > 0 else 0)

    c2 = create_card(s3, Inches(4.8), Inches(1.8), Inches(3.7), Inches(4.9), "Pemisahan Antrean Kasubag TU", WHITE, BORDER_COLOR)
    tb2 = s3.shapes.add_textbox(Inches(5.0), Inches(2.4), Inches(3.3), Inches(4.1))
    tf2 = tb2.text_frame
    tf2.word_wrap = True
    tf2.margin_left = tf2.margin_top = 0
    bullets2 = [
        ("Kategori Terpisah:", "Menu Verifikasi Permintaan dibagi: 'Persediaan (Stok)' vs 'Pengadaan (Beli Baru)'."),
        ("Verifikasi Parsial:", "Kasubag TU dapat menyetujui kuantitas parsial (approved quantity) per item."),
        ("Pembelian Offline TU:", "Input nomor faktur pembelian, tanggal faktur, dan auto-registrasi ke Master Produk."),
        ("Tidak Masuk PIC:", "Pengadaan barang baru tidak langsung masuk antrean distribusi sebelum dibeli secara fisik.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets2):
        p = tf2.paragraphs[0] if i == 0 else tf2.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(10.5)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(10 if i > 0 else 0)

    c3 = create_card(s3, Inches(8.8), Inches(1.8), Inches(3.7), Inches(4.9), "Modul Rekapitulasi & Cetak Resmi", WHITE, BORDER_COLOR)
    tb3 = s3.shapes.add_textbox(Inches(9.0), Inches(2.4), Inches(3.3), Inches(4.1))
    tf3 = tb3.text_frame
    tf3.word_wrap = True
    tf3.margin_left = tf3.margin_top = 0
    bullets3 = [
        ("Dashboard Rekapitulasi:", "Rekap permohonan ATK & Suplais Komputer komprehensif untuk Kasubag & Arsiparis."),
        ("Filter Fleksibel:", "Filter berdasarkan periode tanggal, bulan, tahun, dan tipe pemohon (Subdit/Pegawai)."),
        ("Format Cetak Dit. Bina Teknik:", "Layout cetak resmi format Kementerian PKP RI lengkap dengan kop dinas."),
        ("Otentikasi Pejabat:", "Mencantumkan tanda tangan Pengguna Barang dan Kasubag Tata Usaha.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets3):
        p = tf3.paragraphs[0] if i == 0 else tf3.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(10.5)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(10 if i > 0 else 0)

    # ==========================================
    # SLIDE 4: HARI 2 (SELASA) - NIP & PENGGUNA
    # ==========================================
    s4 = prs.slides.add_slide(blank_layout)
    apply_bg(s4)
    add_header(s4, "Hari 2 (Selasa, 06 Okt 2026): NIP ASN & Manajemen Pengguna", "SPRINT HARI KE-2", "Memperkuat identitas resmi ASN, pengelolaan akun mandiri Kasubag TU, dan reset password aman")
    add_footer(s4, 4)

    # 2 Big Cards + 2 Stats
    c1 = create_card(s4, Inches(0.8), Inches(1.8), Inches(5.7), Inches(3.5), "Integrasi NIP & Manajemen Pengguna Kasubag TU", WHITE, BORDER_COLOR)
    tb = s4.shapes.add_textbox(Inches(1.05), Inches(2.4), Inches(5.2), Inches(2.7))
    tf = tb.text_frame
    tf.word_wrap = True
    bullets_s4_1 = [
        ("Kewajiban NIP ASN:", "Kolom NIP resmi ditambahkan pada registrasi akun baru, database pengguna, serta seluruh nota berita acara dinas."),
        ("Master Pengguna Kasubag TU:", "Kasubag TU kini memiliki wewenang mandiri membuat akun untuk 4 PIC Unit Kerja (SDK, BM, KSPST, TU), Subdit, maupun Pegawai."),
        ("Penyesuaian Hak Akses:", "Mencegah duplikasi akun dan menjamin setiap akun terhubung dengan unit kerja definitif.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets_s4_1):
        p = tf.paragraphs[0] if i == 0 else tf.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(10.5)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(10 if i > 0 else 0)

    c2 = create_card(s4, Inches(6.8), Inches(1.8), Inches(5.7), Inches(3.5), "Fitur Reset Password Mandiri Berbasis NIP", WHITE, BORDER_COLOR)
    tb2 = s4.shapes.add_textbox(Inches(7.05), Inches(2.4), Inches(5.2), Inches(2.7))
    tf2 = tb2.text_frame
    tf2.word_wrap = True
    bullets_s4_2 = [
        ("Self-Service Recovery:", "Pengguna atau PIC yang lupa password dapat mereset mandiri tanpa harus menghubungi admin database."),
        ("Validasi Ganda (2-Factor):", "Sistem memvalidasi kombinasi Email Dinas dan NIP yang terdaftar sebelum mengizinkan penggantian password."),
        ("Keamanan Tinggi:", "Mencegah pembajakan akun dan meminimalisir waktu henti operasional saat kendala kredensial.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets_s4_2):
        p = tf2.paragraphs[0] if i == 0 else tf2.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(10.5)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(10 if i > 0 else 0)

    # Bottom Highlight Card (SweetAlert2 & UI)
    c_bot = create_card(s4, Inches(0.8), Inches(5.5), Inches(11.7), Inches(1.3), "Penyempurnaan Antarmuka: SweetAlert2 & Feedback Interaktif", WHITE, BORDER_COLOR, BLUE)
    tb_b = s4.shapes.add_textbox(Inches(1.05), Inches(6.0), Inches(11.2), Inches(0.7))
    tf_b = tb_b.text_frame
    tf_b.word_wrap = True
    p = tf_b.paragraphs[0]
    p.text = "Menggantikan dialog browser standar dengan modal konfirmasi modern SweetAlert2. Dilengkapi loading state tombol saat proses simpan, notifikasi toast sukses, serta layout sidebar navigasi yang responsif pada resolusi desktop maupun layar mini."
    p.font.size = Pt(10.5)
    p.font.color.rgb = TEXT_DARK

    # ==========================================
    # SLIDE 5: HARI 3 (RABU) - NOTIFIKASI & LEGALITAS
    # ==========================================
    s5 = prs.slides.add_slide(blank_layout)
    apply_bg(s5)
    add_header(s5, "Hari 3 (Rabu, 07 Okt 2026): Notifikasi Terpusat & Legalitas Dokumen", "SPRINT HARI KE-3", "Transparansi status permohonan real-time dan standardisasi gelar akademik ASN")
    add_footer(s5, 5)

    c1 = create_card(s5, Inches(0.8), Inches(1.8), Inches(5.7), Inches(4.9), "Sistem Notifikasi Lonceng Terpusat (Navbar)", WHITE, BORDER_COLOR)
    tb = s5.shapes.add_textbox(Inches(1.05), Inches(2.4), Inches(5.2), Inches(4.1))
    tf = tb.text_frame
    tf.word_wrap = True
    bullets_s5_1 = [
        ("Komunikasi Asinkron:", "Tabel database user_notifications mencatat setiap peristiwa permohonan secara real-time."),
        ("Icon Lonceng Interaktif:", "Menampilkan badge unread counter merah di navbar atas untuk seluruh pengguna."),
        ("Notifikasi Otomatis:", "Notifikasi langsung terkirim saat usulan disetujui Kasubag, ditolak pimpinan, atau diserahkan PIC."),
        ("Fitur Tandai Dibaca:", "Pengguna dapat mengklik notifikasi untuk langsung menuju tiket terkait dan menandai baca.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets_s5_1):
        p = tf.paragraphs[0] if i == 0 else tf.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(11)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(12 if i > 0 else 0)

    c2 = create_card(s5, Inches(6.8), Inches(1.8), Inches(5.7), Inches(4.9), "Legalitas Dokumen & Sinkronisasi Stok Mutasi", WHITE, BORDER_COLOR)
    tb2 = s5.shapes.add_textbox(Inches(7.05), Inches(2.4), Inches(5.2), Inches(4.1))
    tf2 = tb2.text_frame
    tf2.word_wrap = True
    bullets_s5_2 = [
        ("Standardisasi Gelar ASN:", "Menambahkan field title pada database profil. Format nama resmi: Fairuz Armina Nur Azizah, S.T. beserta NIP lengkap."),
        ("Kesesuaian Tata Naskah Dinas:", "Berita acara dan tanda terima pengeluaran barang kini memenuhi kaidah hukum keabsahan dokumen kementerian."),
        ("Sinkronisasi Real-Time Mutasi:", "Aksi CRUD master produk otomatis menyelaraskan histori mutasi barang masuk & saldo persediaan tanpa delay."),
        ("Penyelarasan Struk Bukti:", "Desain struk bukti pengambilan barang diperbaiki agar rapi saat dicetak dalam format slip kecil.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets_s5_2):
        p = tf2.paragraphs[0] if i == 0 else tf2.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(11)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(12 if i > 0 else 0)

    # ==========================================
    # SLIDE 6: HARI 4 (KAMIS) - PENYEKATAN PIC DISTRIBUSI
    # ==========================================
    s6 = prs.slides.add_slide(blank_layout)
    apply_bg(s6)
    add_header(s6, "Hari 4 (Kamis, 08 Okt 2026): Penyekatan Distribusi Multi-Divisi", "SPRINT HARI KE-4 (BAGIAN 1)", "Pemisahan zona distribusi per unit kerja dan pemurnian peran manajerial Kasubag TU")
    add_footer(s6, 6)

    # Left: Explanation, Right: 4 Division Cards
    c_left = create_card(s6, Inches(0.8), Inches(1.8), Inches(4.8), Inches(4.9), "Tata Kelola Distribusi Berbasis Divisi", WHITE, BORDER_COLOR)
    tb = s6.shapes.add_textbox(Inches(1.05), Inches(2.4), Inches(4.3), Inches(4.1))
    tf = tb.text_frame
    tf.word_wrap = True
    bullets_s6 = [
        ("Masalah Sebelumnya:", "Seluruh barang disetujui tercampur dalam satu halaman distribusi umum; Kasubag TU juga memiliki tombol distribusi yang rentan salah klik."),
        ("Penyekatan Ketat PIC:", "Setiap akun PIC Distribusi kini HANYA menerima dan menyerahkan barang milik divisinya masing-masing."),
        ("Pencabutan Akses Kasubag TU:", "Menu Distribusi Barang dihapus dari sidebar Kasubag TU. Kasubag murni fokus sebagai Approver & Pengawas."),
        ("Notifikasi Lonceng PIC:", "Role PIC Distribusi kini mendapatkan icon notifikasi lonceng di navbar seperti role lainnya.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets_s6):
        p = tf.paragraphs[0] if i == 0 else tf.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(10.5)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(10 if i > 0 else 0)

    # Right: 4 Division PIC Cards
    divs = [
        ("PIC SUBDIT SDK", "Sumber Daya Konstruksi", "Hanya mengelola permohonan kolektif dari Tim Subdit SDK.", BLUE),
        ("PIC SUBDIT BM", "Bina Bangunan & Manajemen", "Hanya mengelola permohonan kolektif dari Tim Subdit BM.", NAVY),
        ("PIC SUBDIT KSPST", "Kemitraan & Sarana Prasarana", "Hanya mengelola permohonan kolektif dari Tim Subdit KSPST.", GREEN),
        ("PIC SUBBAG TU", "Tata Usaha & Pegawai", "Mengelola permohonan kolektif TU dan seluruh Pegawai Individu.", AMBER)
    ]
    for idx, (p_title, p_unit, p_desc, p_col) in enumerate(divs):
        py = Inches(1.8) + idx * Inches(1.25)
        card_d = s6.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(5.9), py, Inches(6.6), Inches(1.1))
        card_d.fill.solid()
        card_d.fill.fore_color.rgb = WHITE
        card_d.line.color.rgb = BORDER_COLOR
        card_d.line.width = Pt(1.2)

        # Left color bar
        cbar = s6.shapes.add_shape(MSO_SHAPE.RECTANGLE, Inches(5.9), py, Inches(0.18), Inches(1.1))
        cbar.fill.solid()
        cbar.fill.fore_color.rgb = p_col
        cbar.line.fill.background()

        tb_d = s6.shapes.add_textbox(Inches(6.25), py + Inches(0.12), Inches(6.1), Inches(0.85))
        tf_d = tb_d.text_frame
        tf_d.word_wrap = True
        tf_d.margin_left = tf_d.margin_top = 0
        p_dt = tf_d.paragraphs[0]
        p_dt.text = f"{p_title} — {p_unit}"
        p_dt.font.size = Pt(11)
        p_dt.font.bold = True
        p_dt.font.color.rgb = p_col

        p_dd = tf_d.add_paragraph()
        p_dd.text = p_desc
        p_dd.font.size = Pt(9.5)
        p_dd.font.color.rgb = TEXT_DARK
        p_dd.space_before = Pt(3)

    # ==========================================
    # SLIDE 7: HARI 4 (KAMIS) - BUKTI FOTO SERAH TERIMA
    # ==========================================
    s7 = prs.slides.add_slide(blank_layout)
    apply_bg(s7)
    add_header(s7, "Hari 4 (Kamis, 08 Okt 2026): Upload Bukti Foto Serah Terima", "SPRINT HARI KE-4 (BAGIAN 2)", "Bukti dokumentasi fisik digital otentik sebagai pertanggungjawaban penyerahan barang")
    add_footer(s7, 7)

    # 3 Cards Layout
    c1 = create_card(s7, Inches(0.8), Inches(1.8), Inches(3.7), Inches(4.9), "Fitur Upload Bukti Foto PIC", WHITE, BORDER_COLOR)
    tb = s7.shapes.add_textbox(Inches(1.0), Inches(2.4), Inches(3.3), Inches(4.1))
    tf = tb.text_frame
    tf.word_wrap = True
    bullets_s7_1 = [
        ("Akuntabilitas Nyata:", "Setiap penyerahan fisik barang kini disertai lampiran foto dokumentasi serah terima langsung di lapangan."),
        ("Multi-Format Image:", "Mendukung kompresi modern WebP, JPG, dan PNG dengan validasi ukuran file maksimal 4MB."),
        ("Dukungan Retroaktif:", "Jika saat penyerahan belum sempat difoto, PIC dapat mengunggah bukti foto susulan kapan saja.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets_s7_1):
        p = tf.paragraphs[0] if i == 0 else tf.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(10.5)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(12 if i > 0 else 0)

    c2 = create_card(s7, Inches(4.8), Inches(1.8), Inches(3.7), Inches(4.9), "Modal Interaktif 'Lihat Bukti Foto'", WHITE, BORDER_COLOR)
    tb2 = s7.shapes.add_textbox(Inches(5.0), Inches(2.4), Inches(3.3), Inches(4.1))
    tf2 = tb2.text_frame
    tf2.word_wrap = True
    bullets_s7_2 = [
        ("Pemeriksaan Transparan:", "Tombol 'Lihat Bukti Foto' tersedia di tabel riwayat Pegawai, Subdit, dan Kasubag TU."),
        ("Zoom & Rincian Modal:", "Menampilkan gambar jernih bersama metadata nomor tiket, nama pemohon, dan tanggal penyerahan."),
        ("Audit Lapangan Cepat:", "Pimpinan dapat langsung memeriksa apakah barang benar-benar telah diterima oleh penerima yang sah.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets_s7_2):
        p = tf2.paragraphs[0] if i == 0 else tf2.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(10.5)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(12 if i > 0 else 0)

    c3 = create_card(s7, Inches(8.8), Inches(1.8), Inches(3.7), Inches(4.9), "Optimalisasi Tanpa Beban Cetak", WHITE, BORDER_COLOR)
    tb3 = s7.shapes.add_textbox(Inches(9.0), Inches(2.4), Inches(3.3), Inches(4.1))
    tf3 = tb3.text_frame
    tf3.word_wrap = True
    bullets_s7_3 = [
        ("Keputusan Desain:", "Bukti foto TIDAK dicetak ke dalam struk kertas kecil agar tidak boros tinta dan merusak kerapian tata letak fisik."),
        ("Paperless Ecosystem:", "Dokumentasi foto tersimpan aman di cloud/storage server dan dapat dibuka kapan pun secara paperless."),
        ("Efisiensi Biaya Operasional:", "Menghemat penggunaan kertas thermal/HVS sekaligus menjaga bukti visual tetap beresolusi tinggi.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets_s7_3):
        p = tf3.paragraphs[0] if i == 0 else tf3.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(10.5)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(12 if i > 0 else 0)

    # ==========================================
    # SLIDE 8: HARI 4 (KAMIS) - KONTROL ANTI-STOCKING
    # ==========================================
    s8 = prs.slides.add_slide(blank_layout)
    apply_bg(s8)
    add_header(s8, "Hari 4 (Kamis, 08 Okt 2026): Kontrol Anti-Stocking Pengadaan", "SPRINT HARI KE-4 (BAGIAN 3)", "Mencegah pegawai atau subdirektorat menimbun barang dengan riwayat usulan komprehensif")
    add_footer(s8, 8)

    # Left: Alert & Concept
    c1 = create_card(s8, Inches(0.8), Inches(1.8), Inches(5.7), Inches(4.9), "Urgensi Kontrol Penimbunan Stok", WHITE, BORDER_COLOR, AMBER)
    tb = s8.shapes.add_textbox(Inches(1.05), Inches(2.4), Inches(5.2), Inches(4.1))
    tf = tb.text_frame
    tf.word_wrap = True
    bullets_s8_1 = [
        ("Tantangan Pengadaan ATK:", "Sering terjadi unit kerja atau pegawai mengajukan pengadaan barang serupa berkali-kali padahal barang terdahulu masih ada di meja mereka."),
        ("Badge 'Pernah Minta Serupa':", "Tampil otomatis dengan warna oranye pada kolom Pemohon jika pemohon pernah mengajukan barang yang sama di masa lampau."),
        ("Histori Jejak Item:", "Di bawah setiap item permohonan terdapat data: 'Pernah Diajukan: X kali (Disetujui: Y Unit) • Terakhir: DD/MM/YYYY'."),
        ("Alert pada Approve Modal:", "Sebelum Kasubag TU menyetujui kuantitas, muncul kotak peringatan penimbunan stok untuk membantu pengambilan keputusan yang bijak.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets_s8_1):
        p = tf.paragraphs[0] if i == 0 else tf.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(10.5)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(10 if i > 0 else 0)

    # Right: Modal Audit Komprehensif
    c2 = create_card(s8, Inches(6.8), Inches(1.8), Inches(5.7), Inches(4.9), "Modal Audit Riwayat Pengadaan Pemohon", WHITE, BORDER_COLOR, BLUE)
    tb2 = s8.shapes.add_textbox(Inches(7.05), Inches(2.4), Inches(5.2), Inches(4.1))
    tf2 = tb2.text_frame
    tf2.word_wrap = True
    bullets_s8_2 = [
        ("Tombol 'History Pengadaan (N)':", "Dapat diklik di setiap baris antrean Verifikasi Pengadaan Kasubag TU."),
        ("4 Metrik Ringkasan:", "Menampilkan Total Usulan Pengadaan, Total Disetujui/Selesai, Total Ditolak, dan Total Seluruh Permintaan pemohon."),
        ("Tabel Akumulasi Barang:", "Daftar seluruh item yang pernah diajukan, total kuantitas diterima, dan indikator kontrol stok: 'Waspada Penimbunan Stok' atau '⚠️ Sedang Diajukan Lagi!'."),
        ("Arsip Lengkap Pengadaan:", "Tabel riwayat seluruh pengadaan lampau pemohon lengkap dengan nomor tiket, tanggal, rincian, status, nomor faktur beli, dan catatan TU.")
    ]
    for i, (b_title, b_desc) in enumerate(bullets_s8_2):
        p = tf2.paragraphs[0] if i == 0 else tf2.add_paragraph()
        p.text = f"• {b_title} {b_desc}"
        p.font.size = Pt(10.5)
        p.font.color.rgb = TEXT_DARK
        p.space_before = Pt(10 if i > 0 else 0)

    # ==========================================
    # SLIDE 9: MATRIKS HAK AKSES & ALUR KERJA
    # ==========================================
    s9 = prs.slides.add_slide(blank_layout)
    apply_bg(s9)
    add_header(s9, "Matriks Hak Akses & Alur Kerja Lintas 5 Peran", "ARSITEKTUR SISTEM", "Struktur pembagian peran yang rapi dan terisolasi demi integritas data operasional")
    add_footer(s9, 9)

    # Table layout for roles
    rows = 6
    cols = 5
    left = Inches(0.8)
    top = Inches(1.8)
    width = Inches(11.7)
    height = Inches(4.9)

    table_shape = s9.shapes.add_table(rows, cols, left, top, width, height)
    table = table_shape.table
    table.columns[0].width = Inches(2.1) # Role
    table.columns[1].width = Inches(2.2) # Unit Kerja
    table.columns[2].width = Inches(2.8) # Hak Akses Utama
    table.columns[3].width = Inches(2.6) # Output Dokumen / Aksi
    table.columns[4].width = Inches(2.0) # Notifikasi

    headers = ["Role / Pengguna", "Cakupan Unit", "Fungsi & Hak Akses Utama", "Output & Bukti Fisik", "Sistem Notifikasi"]
    for j, h in enumerate(headers):
        cell = table.cell(0, j)
        cell.fill.solid()
        cell.fill.fore_color.rgb = NAVY
        p = cell.text_frame.paragraphs[0]
        p.text = h
        p.font.size = Pt(10.5)
        p.font.bold = True
        p.font.color.rgb = WHITE
        p.alignment = PP_ALIGN.CENTER

    data = [
        ("Kasubag TU (Approver)", "Subbagian Tata Usaha", "Verifikasi Pengadaan & Stok, Kelola User, Pembelian Offline, Audit Anti-Stocking", "Persetujuan, Faktur Beli, Rekapitulasi Dinas", "Lonceng Navbar + Counter"),
        ("PIC Distribusi SDK", "Subdirektorat SDK", "Menerima barang disetujui SDK, Penyerahan fisik barang ke pemohon SDK", "Upload Bukti Foto, Struk Distribusi SDK", "Lonceng Navbar + Counter"),
        ("PIC Distribusi BM / KSPST", "Subdit BM / KSPST", "Menerima barang disetujui divisi, Penyerahan fisik ke pemohon divisinya", "Upload Bukti Foto, Struk Divisi Terkait", "Lonceng Navbar + Counter"),
        ("PIC Distribusi TU", "Subbag TU & Seluruh Pegawai", "Menerima barang disetujui TU dan seluruh Pegawai Individu", "Upload Bukti Foto, Struk Distribusi Pegawai", "Lonceng Navbar + Counter"),
        ("Pegawai & Subdit", "Unit Kerja Pemohon", "Pengajuan Stok & Usulan Pengadaan Beli Baru, Pantau Status, Reset Sandi NIP", "Tiket Permohonan, Cek Bukti Foto Serah Terima", "Lonceng Navbar + Counter")
    ]

    for i, row in enumerate(data):
        for j, val in enumerate(row):
            cell = table.cell(i + 1, j)
            cell.fill.solid()
            cell.fill.fore_color.rgb = WHITE if i % 2 == 0 else RGBColor(241, 245, 249)
            p = cell.text_frame.paragraphs[0]
            p.text = val
            p.font.size = Pt(9.5)
            p.font.color.rgb = TEXT_DARK
            if j == 0:
                p.font.bold = True
                p.font.color.rgb = NAVY

    # ==========================================
    # SLIDE 10: METRIK, UJI KUALITAS & DAMPAK
    # ==========================================
    s10 = prs.slides.add_slide(blank_layout)
    apply_bg(s10)
    add_header(s10, "Kualitas Sistem & Dampak Operasional Organisasi", "HASIL & DAMPAK NYATA", "Stabilitas teruji dan efisiensi anggaran pengadaan ATK Kementerian PKP")
    add_footer(s10, 10)

    # 4 KPI Stat Boxes
    kpis = [
        ("77 TESTS", "100% PASS", "Seluruh skenario fitur lulus uji otomatis tanpa celah bug.", GREEN),
        ("4 DIVISI", "TERISOLASI", "Penyekatan tuntas PIC SDK, BM, KSPST, & TU.", BLUE),
        ("ANTI-STOCKING", "TERKONTROL", "Pengawasan riwayat menghentikan penimbunan barang di meja.", AMBER),
        ("BUKTI FOTO", "TERDOKUMENTASI", "Serah terima barang 100% dapat dipertanggungjawabkan.", NAVY)
    ]

    kw = Inches(2.78)
    kh = Inches(1.5)
    for i, (k_val, k_sub, k_desc, k_col) in enumerate(kpis):
        kx = Inches(0.8) + i * (kw + Inches(0.18))
        card_k = s10.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, kx, Inches(1.8), kw, kh)
        card_k.fill.solid()
        card_k.fill.fore_color.rgb = WHITE
        card_k.line.color.rgb = k_col
        card_k.line.width = Pt(1.5)

        tb_k = s10.shapes.add_textbox(kx + Inches(0.1), Inches(1.88), kw - Inches(0.2), kh - Inches(0.2))
        tf_k = tb_k.text_frame
        tf_k.word_wrap = True
        tf_k.margin_left = tf_k.margin_top = 0
        p_v = tf_k.paragraphs[0]
        p_v.text = k_val
        p_v.font.size = Pt(18)
        p_v.font.bold = True
        p_v.font.color.rgb = k_col
        p_v.alignment = PP_ALIGN.CENTER

        p_s = tf_k.add_paragraph()
        p_s.text = k_sub
        p_s.font.size = Pt(10)
        p_s.font.bold = True
        p_s.font.color.rgb = TEXT_DARK
        p_s.alignment = PP_ALIGN.CENTER

        p_d = tf_k.add_paragraph()
        p_d.text = k_desc
        p_d.font.size = Pt(8.5)
        p_d.font.color.rgb = TEXT_MUTED
        p_d.alignment = PP_ALIGN.CENTER
        p_d.space_before = Pt(2)

    # 3 Strategic Benefits
    benefits = [
        ("Akuntabilitas & Integritas BMN", [
            "Setiap pengeluaran barang tercatat lengkap dengan identitas ASN (Nama + Gelar + NIP).",
            "Bukti foto serah terima fisik mengeliminasi klaim barang fiktif atau hilang tanpa jejak.",
            "Berita acara resmi memenuhi standar pemeriksaan Inspektorat / BPK RI."
        ]),
        ("Efisiensi & Ketepatan Distribusi", [
            "Penyekatan per divisi menjamin PIC hanya fokus pada rekan satu unit kerjanya.",
            "Waktu tunggu distribusi dipangkas berkat notifikasi lonceng navbar yang responsif.",
            "Kasubag TU tidak lagi dibebani tugas teknis penyerahan fisik barang."
        ]),
        ("Penghematan Anggaran Pengadaan", [
            "Fitur kontrol anti-stocking mencegah usulan ganda barang yang belum habis.",
            "Kasubag TU dapat mengevaluasi kuantitas yang wajar berdasarkan histori akumulasi barang.",
            "Data mutasi real-time menyajikan saldo persediaan gudang yang akurat setiap saat."
        ])
    ]

    bw = Inches(3.7)
    bh = Inches(3.1)
    for i, (b_title, b_items) in enumerate(benefits):
        bx = Inches(0.8) + i * (bw + Inches(0.25))
        by = Inches(3.6)
        card_b = create_card(s10, bx, by, bw, bh, b_title, WHITE, BORDER_COLOR, NAVY)

        tb_b = s10.shapes.add_textbox(bx + Inches(0.2), by + Inches(0.65), bw - Inches(0.4), bh - Inches(0.8))
        tf_b = tb_b.text_frame
        tf_b.word_wrap = True
        tf_b.margin_left = tf_b.margin_top = 0
        for idx, itm in enumerate(b_items):
            p = tf_b.paragraphs[0] if idx == 0 else tf_b.add_paragraph()
            p.text = f"• {itm}"
            p.font.size = Pt(10)
            p.font.color.rgb = TEXT_DARK
            p.space_before = Pt(8 if idx > 0 else 0)

    # ==========================================
    # SLIDE 11: PENUTUP & KESIAPAN DATA ASLI
    # ==========================================
    s11 = prs.slides.add_slide(blank_layout)
    bg11 = s11.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, prs.slide_width, prs.slide_height)
    bg11.fill.solid()
    bg11.fill.fore_color.rgb = NAVY_DARK
    bg11.line.fill.background()

    # Logo on center
    if os.path.exists(LOGO_PATH):
        s11.shapes.add_picture(LOGO_PATH, Inches(6.0), Inches(1.1), Inches(1.3), Inches(1.3))

    tb_c = s11.shapes.add_textbox(Inches(1.5), Inches(2.6), Inches(10.3), Inches(3.0))
    tf_c = tb_c.text_frame
    tf_c.word_wrap = True
    p = tf_c.paragraphs[0]
    p.text = "SIAP DIGUNAKAN UNTUK DATA OPERASIONAL ASLI"
    p.font.size = Pt(26)
    p.font.bold = True
    p.font.color.rgb = RGBColor(56, 189, 248)
    p.alignment = PP_ALIGN.CENTER

    p2 = tf_c.add_paragraph()
    p2.text = "Seluruh akun uji coba telah dibersihkan. Sistem ATEKA kini siap beroperasi penuh mendampingi pengelolaan inventori & pengadaan ATK di lingkungan Direktorat Bina Teknik Perumahan dan Kawasan Permukiman."
    p2.font.size = Pt(14)
    p2.font.color.rgb = RGBColor(224, 242, 254)
    p2.alignment = PP_ALIGN.CENTER
    p2.space_before = Pt(12)

    # Final Summary Checklist Card
    card_sum = s11.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(2.5), Inches(4.3), Inches(8.333), Inches(1.6))
    card_sum.fill.solid()
    card_sum.fill.fore_color.rgb = RGBColor(15, 23, 42)
    card_sum.line.color.rgb = RGBColor(56, 189, 248)
    card_sum.line.width = Pt(1.2)

    tb_s = s11.shapes.add_textbox(Inches(2.7), Inches(4.45), Inches(7.9), Inches(1.3))
    tf_s = tb_s.text_frame
    tf_s.word_wrap = True
    p_s1 = tf_s.paragraphs[0]
    p_s1.text = "✓ 5 Role Terintegrasi: Kasubag TU, PIC Distribusi (4 Divisi), Subdit, Pegawai, & Arsiparis"
    p_s1.font.size = Pt(11)
    p_s1.font.bold = True
    p_s1.font.color.rgb = WHITE

    p_s2 = tf_s.add_paragraph()
    p_s2.text = "✓ Jalur Pengadaan Tertib: Form Usulan Beli Baru + Input Faktur Kasubag TU"
    p_s2.font.size = Pt(11)
    p_s2.font.color.rgb = RGBColor(186, 230, 253)
    p_s2.space_before = Pt(4)

    p_s3 = tf_s.add_paragraph()
    p_s3.text = "✓ Keamanan & Akuntabilitas Penuh: NIP ASN, Foto Serah Terima, & Kontrol Anti-Stocking"
    p_s3.font.size = Pt(11)
    p_s3.font.color.rgb = RGBColor(186, 230, 253)
    p_s3.space_before = Pt(4)

    # Q&A Text
    tb_qa = s11.shapes.add_textbox(Inches(2.0), Inches(6.2), Inches(9.333), Inches(0.6))
    tf_qa = tb_qa.text_frame
    p_qa = tf_qa.paragraphs[0]
    p_qa.text = "Terima Kasih — Ada Tanggapan atau Pertanyaan?"
    p_qa.font.size = Pt(16)
    p_qa.font.bold = True
    p_qa.font.color.rgb = WHITE
    p_qa.alignment = PP_ALIGN.CENTER

    output_path = os.path.abspath("Presentasi_Fitur_Update_ATEKA_4_Hari.pptx")
    prs.save(output_path)
    print(f"Presentation saved successfully to: {output_path}")

if __name__ == "__main__":
    create_presentation()
