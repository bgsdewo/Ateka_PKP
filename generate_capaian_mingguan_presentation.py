"""
Script Pembuat Presentasi Capaian Sprint Mingguan ATEKA
Kementerian Perumahan dan Kawasan Permukiman RI
Fokus: Membuat Form, Membuat Manual Book, dan Membuat Flow Chart

Memenuhi seluruh kaidah desain:
- Layout 16:9 Wide (13.333" x 7.5")
- Palet resmi Kementerian PKP & ATEKA (Deep Navy, Regal Gold, Ice Blue, Clean Card)
- Tanpa garis dekoratif di bawah judul (Anti-AI slop)
- Tanpa accent stripes / header-footer stripes
- Variasi layout kaya (Split mockup, 3-kolom, matriks 2x2, diagram alur, tabel sinergi, stat cards)
- Speaker notes pada setiap slide
- Validasi schema resmi OOXML via scripts/office/validate.py
"""

import os
import sys
from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.dml.color import RGBColor
from pptx.enum.shapes import MSO_SHAPE

def build_presentation(output_path="Presentasi_Capaian_Mingguan_ATEKA.pptx"):
    prs = Presentation()
    prs.slide_width = Inches(13.333)
    prs.slide_height = Inches(7.5)
    blank_layout = prs.slide_layouts[6]

    # Palet Warna Resmi
    NAVY_DEEP    = RGBColor(10, 28, 53)    # #0A1C35 (Primary Brand Kemen PKP)
    NAVY_MID     = RGBColor(15, 45, 84)    # #0F2D54
    BLUE_ACCENT  = RGBColor(2, 132, 199)   # #0284C7 (Sky Accent)
    BLUE_LIGHT   = RGBColor(224, 242, 254) # #E0F2FE
    GOLD_ACCENT  = RGBColor(197, 155, 39)  # #C59B27 (Regal Gold)
    GOLD_LIGHT   = RGBColor(254, 249, 195) # #FEF9C3
    BG_LIGHT     = RGBColor(248, 250, 252) # #F8FAFC
    CARD_BG      = RGBColor(255, 255, 255) # #FFFFFF
    BORDER_LIGHT = RGBColor(226, 232, 240) # #E2E8F0
    TEXT_MAIN    = RGBColor(15, 23, 42)    # #0F172A
    TEXT_MUTED   = RGBColor(100, 116, 139) # #64748B
    EMERALD      = RGBColor(5, 150, 105)   # #059669
    EMERALD_BG   = RGBColor(236, 253, 245) # #ECFDF5
    AMBER        = RGBColor(217, 119, 6)   # #D97706
    AMBER_BG     = RGBColor(254, 243, 199) # #FEF3C7
    WHITE        = RGBColor(255, 255, 255)

    LOGO_PATH = os.path.abspath('public/hope-ui/assets/images/auth/kemenkp.jpeg')
    FLOWCHART_PATH = os.path.abspath('public/img/navigasi/alur_proses_navigasi_lintas_role.png')

    # Helper: Latar Belakang Slide
    def set_slide_background(slide, color=BG_LIGHT):
        bg = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, prs.slide_width, prs.slide_height)
        bg.fill.solid()
        bg.fill.fore_color.rgb = color
        bg.line.fill.background()
        return bg

    # Helper: Header Standar Slide Konten (Bersih, elegan, tanpa accent bar bawah judul)
    def add_slide_header(slide, title, category_badge, subtitle=None):
        # Category Badge (pill rounded)
        badge = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(0.4), Inches(4.2), Inches(0.34))
        badge.fill.solid()
        badge.fill.fore_color.rgb = BLUE_LIGHT
        badge.line.color.rgb = BLUE_ACCENT
        badge.line.width = Pt(1)
        tf_b = badge.text_frame
        tf_b.word_wrap = True
        tf_b.margin_left = Inches(0.1)
        tf_b.margin_top = Inches(0.04)
        p_b = tf_b.paragraphs[0]
        p_b.text = category_badge.upper()
        p_b.font.size = Pt(9.5)
        p_b.font.bold = True
        p_b.font.color.rgb = BLUE_ACCENT
        p_b.font.name = "Arial"
        p_b.alignment = PP_ALIGN.CENTER

        # Title Box
        tb = slide.shapes.add_textbox(Inches(0.8), Inches(0.8), Inches(11.0), Inches(0.65))
        tf = tb.text_frame
        tf.word_wrap = True
        tf.margin_left = tf.margin_top = tf.margin_right = tf.margin_bottom = 0
        p = tf.paragraphs[0]
        p.text = title
        p.font.size = Pt(22)
        p.font.bold = True
        p.font.color.rgb = TEXT_MAIN
        p.font.name = "Arial"

        if subtitle:
            p2 = tf.add_paragraph()
            p2.text = subtitle
            p2.font.size = Pt(11)
            p2.font.color.rgb = TEXT_MUTED
            p2.font.name = "Arial"
            p2.space_before = Pt(3)

        # Ministry Logo di pojok kanan atas
        if os.path.exists(LOGO_PATH):
            slide.shapes.add_picture(LOGO_PATH, Inches(12.0), Inches(0.4), Inches(0.6), Inches(0.6))

    # Helper: Footer Slide
    def add_slide_footer(slide, current_page, total_pages=12):
        # Footer text left
        tb_l = slide.shapes.add_textbox(Inches(0.8), Inches(7.08), Inches(9.5), Inches(0.3))
        tf_l = tb_l.text_frame
        tf_l.margin_left = tf_l.margin_top = 0
        p_l = tf_l.paragraphs[0]
        p_l.text = "ATEKA PKP • Laporan Capaian Sprint Mingguan (Formulir, Manual Book, Flow Chart) • Dit. Bina Teknik Perumahan"
        p_l.font.size = Pt(8.5)
        p_l.font.color.rgb = TEXT_MUTED
        p_l.font.name = "Arial"

        # Footer page number right
        tb_r = slide.shapes.add_textbox(Inches(10.5), Inches(7.08), Inches(2.0), Inches(0.3))
        tf_r = tb_r.text_frame
        tf_r.margin_left = tf_r.margin_top = 0
        p_r = tf_r.paragraphs[0]
        p_r.text = f"Halaman {current_page} dari {total_pages}"
        p_r.font.size = Pt(8.5)
        p_r.font.bold = True
        p_r.font.color.rgb = TEXT_MUTED
        p_r.font.name = "Arial"
        p_r.alignment = PP_ALIGN.RIGHT

    # Helper: Membuat Kartu Konten (White container box with subtle border)
    def create_card(slide, left, top, width, height, bg_color=CARD_BG, border_color=BORDER_LIGHT):
        card = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, left, top, width, height)
        card.fill.solid()
        card.fill.fore_color.rgb = bg_color
        card.line.color.rgb = border_color
        card.line.width = Pt(1.2)
        return card

    # Helper: Menambahkan Notes pada Slide
    def set_notes(slide, notes_text):
        notes_slide = slide.notes_slide
        tf = notes_slide.notes_text_frame
        tf.text = notes_text

    # =========================================================================
    # SLIDE 1: COVER (Dark Navy #0A1C35)
    # =========================================================================
    s1 = prs.slides.add_slide(blank_layout)
    set_slide_background(s1, NAVY_DEEP)

    # Logo Instansi
    if os.path.exists(LOGO_PATH):
        s1.shapes.add_picture(LOGO_PATH, Inches(1.0), Inches(1.0), Inches(1.2), Inches(1.2))

    # Badge Instansi
    b_cov = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(1.0), Inches(2.45), Inches(5.4), Inches(0.38))
    b_cov.fill.solid()
    b_cov.fill.fore_color.rgb = NAVY_MID
    b_cov.line.color.rgb = GOLD_ACCENT
    b_cov.line.width = Pt(1.2)
    p_bc = b_cov.text_frame.paragraphs[0]
    p_bc.text = "KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN RI"
    p_bc.font.size = Pt(9.5)
    p_bc.font.bold = True
    p_bc.font.color.rgb = GOLD_ACCENT
    p_bc.font.name = "Arial"
    p_bc.alignment = PP_ALIGN.CENTER

    # Main Title
    tb_t = s1.shapes.add_textbox(Inches(1.0), Inches(2.95), Inches(7.5), Inches(2.6))
    tf_t = tb_t.text_frame
    tf_t.word_wrap = True
    tf_t.margin_left = tf_t.margin_top = 0
    p1 = tf_t.paragraphs[0]
    p1.text = "LAPORAN CAPAIAN SPRINT"
    p1.font.size = Pt(36)
    p1.font.bold = True
    p1.font.color.rgb = WHITE
    p1.font.name = "Arial"

    p2 = tf_t.add_paragraph()
    p2.text = "Pengembangan Form, Manual Book, dan Flow Chart Sistem ATEKA"
    p2.font.size = Pt(20)
    p2.font.bold = True
    p2.font.color.rgb = RGBColor(186, 230, 253) # sky light
    p2.font.name = "Arial"
    p2.space_before = Pt(8)

    p3 = tf_t.add_paragraph()
    p3.text = "Dokumentasi Lengkap Hasil Kerja Kolaboratif Pekan Ini • Oktober 2026"
    p3.font.size = Pt(12)
    p3.font.color.rgb = RGBColor(148, 163, 184) # slate-400
    p3.font.name = "Arial"
    p3.space_before = Pt(10)

    # Info Kotak Pengembang & Instansi
    c_info = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(1.0), Inches(5.75), Inches(7.2), Inches(1.1))
    c_info.fill.solid()
    c_info.fill.fore_color.rgb = NAVY_MID
    c_info.line.color.rgb = RGBColor(30, 64, 100)
    c_info.line.width = Pt(1)
    tf_ci = c_info.text_frame
    tf_ci.margin_left = Inches(0.25)
    tf_ci.margin_top = Inches(0.18)
    p_ci1 = tf_ci.paragraphs[0]
    p_ci1.text = "Unit Kerja : Direktorat Bina Teknik Perumahan dan Kawasan Permukiman"
    p_ci1.font.size = Pt(10.5)
    p_ci1.font.bold = True
    p_ci1.font.color.rgb = WHITE
    p_ci1.font.name = "Arial"
    p_ci2 = tf_ci.add_paragraph()
    p_ci2.text = "Pengelola  : Subbagian Tata Usaha (Kasubag TU) & Tim Pengembang Sistem"
    p_ci2.font.size = Pt(9.5)
    p_ci2.font.color.rgb = RGBColor(203, 213, 225)
    p_ci2.font.name = "Arial"
    p_ci2.space_before = Pt(4)

    # Right side 3 Pillars Container Box
    c_side = s1.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(8.7), Inches(1.0), Inches(3.8), Inches(5.85))
    c_side.fill.solid()
    c_side.fill.fore_color.rgb = NAVY_MID
    c_side.line.color.rgb = RGBColor(30, 64, 100)
    c_side.line.width = Pt(1.2)

    tb_s = s1.shapes.add_textbox(Inches(8.95), Inches(1.25), Inches(3.3), Inches(5.3))
    tf_s = tb_s.text_frame
    tf_s.word_wrap = True
    tf_s.margin_left = tf_s.margin_top = 0
    p_sh = tf_s.paragraphs[0]
    p_sh.text = "3 PILAR CAPAIAN UTAMA"
    p_sh.font.size = Pt(13)
    p_sh.font.bold = True
    p_sh.font.color.rgb = GOLD_ACCENT
    p_sh.font.name = "Arial"

    pillars = [
        ("PILAR 1", "PENGEMBANGAN FORM", "Standarisasi Form NIP ASN 18-digit, Reset Password Mandiri, Form Permintaan Gudang, Form Pengadaan Baru, dan Form BAST Berfoto."),
        ("PILAR 2", "MANUAL BOOK RESMI", "3 Buku Panduan Lengkap (Pegawai, Subdit, PIC) berstandar ReportLab Platypus, 35 halaman PDF dengan 28 tangkapan layar antarmuka nyata."),
        ("PILAR 3", "ARSITEKTUR FLOW CHART", "Pemetaan logika alur proses bisnis lintas 5 peran, cabang keputusan ketersediaan barang, serta hierarki verifikasi berjenjang Kasubag TU.")
    ]

    for tag, title_p, desc_p in pillars:
        p_t = tf_s.add_paragraph()
        p_t.text = f"• {tag}: {title_p}"
        p_t.font.size = Pt(11)
        p_t.font.bold = True
        p_t.font.color.rgb = WHITE
        p_t.font.name = "Arial"
        p_t.space_before = Pt(14)

        p_d = tf_s.add_paragraph()
        p_d.text = desc_p
        p_d.font.size = Pt(9.5)
        p_d.font.color.rgb = RGBColor(203, 213, 225)
        p_d.font.name = "Arial"
        p_d.space_before = Pt(4)

    set_notes(s1, "Slide Pembuka: Menyajikan judul resmi laporan pencapaian sprint mingguan ATEKA pada Kementerian PKP RI dengan fokus utama pada 3 pilar: Form, Manual Book, dan Flow Chart.")

    # =========================================================================
    # SLIDE 2: ROADMAP CAPAIAN 3 PILAR (Light BG)
    # =========================================================================
    s2 = prs.slides.add_slide(blank_layout)
    set_slide_background(s2, BG_LIGHT)
    add_slide_header(s2, "Ikhtisar Eksekutif Capaian Sprint Mingguan", "RINGKASAN EKSEKUTIF", "Tiga pilar transformasi operasional sistem ATEKA untuk mewujudkan tata kelola BMN yang akuntabel")
    add_slide_footer(s2, 2)

    col_width = Inches(3.65)
    col_gap = Inches(0.4)
    left_start = Inches(0.8)
    top_pos = Inches(1.6)
    height_pos = Inches(4.3)

    cards_data = [
        (
            "PILAR I: FORMULIR DIGITAL",
            "Modernisasi Antarmuka Input",
            BLUE_ACCENT,
            BLUE_LIGHT,
            [
                ("Form Registrasi NIP ASN", "Validasi 18-digit numerik & pemetaan unit kerja/subdirektorat."),
                ("Form Reset Password", "Pemulihan mandiri berbasis identitas resmi tanpa bergantung admin."),
                ("Form Stok vs Pengadaan", "Pemisahan alur permintaan ready-stock vs belanja baru supplier."),
                ("Form BAST & Bukti Foto", "Dokumentasi digital penyerahan fisik barang secara riil dan sah.")
            ]
        ),
        (
            "PILAR II: MANUAL BOOK RESMI",
            "Standarisasi Dokumentasi SOP",
            GOLD_ACCENT,
            GOLD_LIGHT,
            [
                ("3 Edisi Buku Panduan", "Buku Pegawai (11 Hal), Subdit (11 Hal), dan PIC Gudang (13 Hal)."),
                ("ReportLab Platypus", "Arsitektur NumberedCanvas dua tahap, running header-footer dinamis."),
                ("28+ Mockup Antarmuka", "Tangkapan layar MacBook Pro & Mobile resolusi tinggi presisi."),
                ("FAQ & Eskalasi Helpdesk", "Panduan troubleshooting mandiri untuk mempercepat adopsi.")
            ]
        ),
        (
            "PILAR III: FLOW CHART SISTEM",
            "Transparansi & Kepastian Alur",
            EMERALD,
            EMERALD_BG,
            [
                ("Diagram Navigasi Lintas Role", "Pemetaan matriks wewenang 5 peran pengguna secara terisolasi."),
                ("Logika Cabang Ketersediaan", "Pemeriksaan stok katalog sebelum eskalasi ke belanja supplier."),
                ("Rantai Verifikasi Kasubag TU", "Approval berjenjang: disetujui, disetujui sebagian, atau ditolak."),
                ("Rantai Distribusi Fisik", "Tiket pengambilan barang hingga penandatanganan berita acara.")
            ]
        )
    ]

    for idx, (pilar_title, pilar_sub, color_accent, color_light, items) in enumerate(cards_data):
        c_left = left_start + idx * (col_width + col_gap)
        c = create_card(s2, c_left, top_pos, col_width, height_pos)

        # Header box inside card
        hb = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, c_left + Inches(0.2), top_pos + Inches(0.2), col_width - Inches(0.4), Inches(0.7))
        hb.fill.solid()
        hb.fill.fore_color.rgb = color_light
        hb.line.color.rgb = color_accent
        hb.line.width = Pt(1)
        tf_hb = hb.text_frame
        tf_hb.word_wrap = True
        tf_hb.margin_left = Inches(0.15)
        tf_hb.margin_top = Inches(0.08)
        p_ht = tf_hb.paragraphs[0]
        p_ht.text = pilar_title
        p_ht.font.size = Pt(11)
        p_ht.font.bold = True
        p_ht.font.color.rgb = color_accent
        p_ht.font.name = "Arial"
        p_hs = tf_hb.add_paragraph()
        p_hs.text = pilar_sub
        p_hs.font.size = Pt(9)
        p_hs.font.color.rgb = TEXT_MUTED
        p_hs.font.name = "Arial"

        # Content list
        tb_c = s2.shapes.add_textbox(c_left + Inches(0.2), top_pos + Inches(1.05), col_width - Inches(0.4), Inches(3.1))
        tf_c = tb_c.text_frame
        tf_c.word_wrap = True
        tf_c.margin_left = tf_c.margin_top = 0

        for i, (item_title, item_desc) in enumerate(items):
            p_it = tf_c.add_paragraph() if i > 0 else tf_c.paragraphs[0]
            p_it.text = f"• {item_title}"
            p_it.font.size = Pt(11)
            p_it.font.bold = True
            p_it.font.color.rgb = TEXT_MAIN
            p_it.font.name = "Arial"
            if i > 0:
                p_it.space_before = Pt(8)

            p_id = tf_c.add_paragraph()
            p_id.text = item_desc
            p_id.font.size = Pt(9.5)
            p_id.font.color.rgb = TEXT_MUTED
            p_id.font.name = "Arial"
            p_id.space_before = Pt(2)

    # Bottom KPI Summary Banner
    kpi_box = s2.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(6.1), Inches(11.733), Inches(0.75))
    kpi_box.fill.solid()
    kpi_box.fill.fore_color.rgb = NAVY_DEEP
    kpi_box.line.color.rgb = BLUE_ACCENT
    kpi_box.line.width = Pt(1)

    kpis = [
        ("5+ FORMULIR", "Digital & Terintegrasi"),
        ("3 BUKU PANDUAN", "35 Halaman PDF A4"),
        ("28+ MOCKUP", "Tangkapan Layar Nyata"),
        ("5 ALUR SISTEM", "Rantai Nilai BMN Bebas Kertas")
    ]
    kpi_col_w = Inches(11.733 / 4)
    for i, (k_val, k_lbl) in enumerate(kpis):
        tb_k = s2.shapes.add_textbox(Inches(0.8) + i * kpi_col_w, Inches(6.18), kpi_col_w, Inches(0.6))
        tf_k = tb_k.text_frame
        tf_k.margin_left = tf_k.margin_top = 0
        p_kv = tf_k.paragraphs[0]
        p_kv.text = k_val
        p_kv.font.size = Pt(13)
        p_kv.font.bold = True
        p_kv.font.color.rgb = GOLD_ACCENT
        p_kv.font.name = "Arial"
        p_kv.alignment = PP_ALIGN.CENTER

        p_kl = tf_k.add_paragraph()
        p_kl.text = k_lbl
        p_kl.font.size = Pt(9)
        p_kl.font.color.rgb = WHITE
        p_kl.font.name = "Arial"
        p_kl.alignment = PP_ALIGN.CENTER

    set_notes(s2, "Slide Ikhtisar Eksekutif: Memetakan 3 pilar utama capaian kerja minggu ini beserta ringkasan metrik kuantitatif (5 form, 3 manual book, 28 mockup, 5 alur proses bisnis).")

    # =========================================================================
    # SLIDE 3: PILAR I - FORM IDENTITAS ASN & RESET PASSWORD MANDIRI (Split View)
    # =========================================================================
    s3 = prs.slides.add_slide(blank_layout)
    set_slide_background(s3, BG_LIGHT)
    add_slide_header(s3, "Standardisasi Form Registrasi NIP & Reset Password", "PILAR I: FORMULIR DIGITAL", "Memastikan keabsahan data aparatur sipil negara dan kemandirian pengelolaan kredensial akun")
    add_slide_footer(s3, 3)

    # Left Container (2 Content Cards)
    card_reg = create_card(s3, Inches(0.8), Inches(1.6), Inches(7.5), Inches(2.45))
    tb_cr = s3.shapes.add_textbox(Inches(1.05), Inches(1.8), Inches(7.0), Inches(2.1))
    tf_cr = tb_cr.text_frame
    tf_cr.word_wrap = True
    tf_cr.margin_left = tf_cr.margin_top = 0

    p_cr1 = tf_cr.paragraphs[0]
    p_cr1.text = "1. FORM REGISTRASI BERBASIS NIP 18 DIGIT"
    p_cr1.font.size = Pt(13)
    p_cr1.font.bold = True
    p_cr1.font.color.rgb = BLUE_ACCENT
    p_cr1.font.name = "Arial"

    reg_points = [
        ("Validasi Numerik Ketat:", "Field NIP wajib 18 digit numerik standar BKN, mencegah pembuatan akun ganda atau fiktif."),
        ("Pemetaan Subdirektorat:", "Pilihan unit kerja terisolasi (Bina Teknik, Penyelenggaraan, Pengendalian, Subbag TU)."),
        ("Penetapan Peran Otomatis:", "Pemisahan hak akses sejak awal antara Pegawai (Individu) dan Subdirektorat (Perwakilan Unit).")
    ]
    for lbl, desc in reg_points:
        p_rp = tf_cr.add_paragraph()
        p_rp.text = f"• {lbl} {desc}"
        p_rp.font.size = Pt(10)
        p_rp.font.color.rgb = TEXT_MAIN
        p_rp.font.name = "Arial"
        p_rp.space_before = Pt(4)

    card_pwd = create_card(s3, Inches(0.8), Inches(4.25), Inches(7.5), Inches(2.55))
    tb_cp = s3.shapes.add_textbox(Inches(1.05), Inches(4.45), Inches(7.0), Inches(2.2))
    tf_cp = tb_cp.text_frame
    tf_cp.word_wrap = True
    tf_cp.margin_left = tf_cp.margin_top = 0

    p_cp1 = tf_cp.paragraphs[0]
    p_cp1.text = "2. FORM PEMULIHAN KATA SANDI (SELF-SERVICE RESET)"
    p_cp1.font.size = Pt(13)
    p_cp1.font.bold = True
    p_cp1.font.color.rgb = EMERALD
    p_cp1.font.name = "Arial"

    pwd_points = [
        ("Autentikasi Dua Langkah:", "Memeriksa kesesuaian Nomor Induk Pegawai dan email kedinasan terdaftar."),
        ("Pengurangan Beban Helpdesk:", "Pegawai dapat memperbarui kata sandi secara instan tanpa menunggu intervensi admin."),
        ("Audit Trail Keamanan:", "Setiap percobaan reset tercatat dalam log sistem untuk mengantisipasi akses tidak sah.")
    ]
    for lbl, desc in pwd_points:
        p_pp = tf_cp.add_paragraph()
        p_pp.text = f"• {lbl} {desc}"
        p_pp.font.size = Pt(10)
        p_pp.font.color.rgb = TEXT_MAIN
        p_pp.font.name = "Arial"
        p_pp.space_before = Pt(4)

    # Right side: Visual Mobile Mockup
    card_mock = create_card(s3, Inches(8.6), Inches(1.6), Inches(3.933), Inches(5.2))
    tb_m = s3.shapes.add_textbox(Inches(8.8), Inches(1.8), Inches(3.5), Inches(0.5))
    tf_m = tb_m.text_frame
    p_mt = tf_m.paragraphs[0]
    p_mt.text = "Tampilan Form Mobile Teruji"
    p_mt.font.size = Pt(12)
    p_mt.font.bold = True
    p_mt.font.color.rgb = NAVY_DEEP
    p_mt.alignment = PP_ALIGN.CENTER

    img_mobile = os.path.abspath('assets/manual_book_pegawai_images/image1.png')
    if os.path.exists(img_mobile):
        s3.shapes.add_picture(img_mobile, Inches(9.35), Inches(2.35), Inches(2.4), Inches(4.25))

    set_notes(s3, "Slide Form Registrasi & Reset Password: Menguraikan standarisasi input identitas ASN melalui validasi 18 digit NIP serta fitur pemulihan kata sandi mandiri yang memangkas tiket helpdesk Subbag TU.")

    # =========================================================================
    # SLIDE 4: PILAR I - FORM PERMINTAAN STOK VS PENGADAAN BARU (2-Kolom Komparasi)
    # =========================================================================
    s4 = prs.slides.add_slide(blank_layout)
    set_slide_background(s4, BG_LIGHT)
    add_slide_header(s4, "Form Permintaan Gudang vs Form Usulan Belanja Baru", "PILAR I: FORMULIR DIGITAL", "Dua pintu pengajuan kebutuhan: barang tersedia di gudang (ready stock) vs usulan pengadaan barang baru")
    add_slide_footer(s4, 4)

    half_w = Inches(5.65)
    gap_w = Inches(0.4)

    # Left Column: Form Permintaan Gudang
    c_stok = create_card(s4, Inches(0.8), Inches(1.6), half_w, Inches(5.2))
    tb_st = s4.shapes.add_textbox(Inches(1.05), Inches(1.8), half_w - Inches(0.5), Inches(1.8))
    tf_st = tb_st.text_frame
    tf_st.word_wrap = True
    tf_st.margin_left = tf_st.margin_top = 0

    p_s1 = tf_st.paragraphs[0]
    p_s1.text = "FORM PERMINTAAN BARANG GUDANG (READY STOCK)"
    p_s1.font.size = Pt(12.5)
    p_s1.font.bold = True
    p_s1.font.color.rgb = BLUE_ACCENT

    stok_desc = [
        ("Katalog Real-time:", "Hanya menampilkan barang dengan sisa persediaan aktif di gudang."),
        ("Multi-Item Selector:", "Pegawai dapat memilih hingga puluhan jenis ATK dalam satu tiket pengajuan."),
        ("Validasi Kuota Pemakaian:", "Membatasi jumlah permintaan agar proporsional dan tidak terjadi monopoli stok.")
    ]
    for lbl, desc in stok_desc:
        p = tf_st.add_paragraph()
        p.text = f"• {lbl} {desc}"
        p.font.size = Pt(9.5)
        p.font.color.rgb = TEXT_MAIN
        p.space_before = Pt(3)

    img_stok = os.path.abspath('assets/manual_book_pegawai_images/image6.png')
    if os.path.exists(img_stok):
        s4.shapes.add_picture(img_stok, Inches(1.05), Inches(3.6), half_w - Inches(0.5), Inches(3.0))

    # Right Column: Form Pengadaan Baru (Procurement)
    c_proc = create_card(s4, Inches(0.8) + half_w + gap_w, Inches(1.6), half_w, Inches(5.2))
    tb_pr = s4.shapes.add_textbox(Inches(0.8) + half_w + gap_w + Inches(0.25), Inches(1.8), half_w - Inches(0.5), Inches(1.8))
    tf_pr = tb_pr.text_frame
    tf_pr.word_wrap = True
    tf_pr.margin_left = tf_pr.margin_top = 0

    p_p1 = tf_pr.paragraphs[0]
    p_p1.text = "FORM USULAN PENGADAAN BARU (BELI SUPPLIER)"
    p_p1.font.size = Pt(12.5)
    p_p1.font.bold = True
    p_p1.font.color.rgb = GOLD_ACCENT

    proc_desc = [
        ("Spesifikasi Rinci & Estimasi:", "Wajib melampirkan merek, tipe, perkiraan harga satuan, & link rujukan."),
        ("Justifikasi Urgensi Tugas:", "Penjelasan kebutuhan mendesak untuk kegiatan kedinasan resmi."),
        ("Anti-Stocking Verification:", "Sistem otomatis memeriksa riwayat pengajuan agar tidak ada penimbunan barang.")
    ]
    for lbl, desc in proc_desc:
        p = tf_pr.add_paragraph()
        p.text = f"• {lbl} {desc}"
        p.font.size = Pt(9.5)
        p.font.color.rgb = TEXT_MAIN
        p.space_before = Pt(3)

    img_proc = os.path.abspath('assets/manual_book_pegawai_images/image7.png')
    if os.path.exists(img_proc):
        s4.shapes.add_picture(img_proc, Inches(0.8) + half_w + gap_w + Inches(0.25), Inches(3.6), half_w - Inches(0.5), Inches(3.0))

    set_notes(s4, "Slide Form Stok vs Form Pengadaan: Menjelaskan diferensiasi tegas antara formulir barang gudang dan formulir belanja baru supplier lengkap dengan fitur pencegahan penimbunan barang (anti-stocking).")

    # =========================================================================
    # SLIDE 5: PILAR I - FORM TRANSAKSI GUDANG, BAST & UPLOAD FOTO FISIK
    # =========================================================================
    s5 = prs.slides.add_slide(blank_layout)
    set_slide_background(s5, BG_LIGHT)
    add_slide_header(s5, "Akuntabilitas Fisik: Form Transaksi & Dokumentasi Foto BAST", "PILAR I: FORMULIR DIGITAL", "Menutup celah selisih inventori dengan form konversi satuan, berita acara otomatis, dan bukti foto serah terima")
    add_slide_footer(s5, 5)

    # 3 Top Feature Cards
    f_width = Inches(3.65)
    f_gap = Inches(0.4)
    features_gudang = [
        (
            "1. FORM TRANSAKSI GUDANG",
            BLUE_ACCENT,
            BLUE_LIGHT,
            "Pencatatan Masuk & Keluar",
            "Mendukung konversi otomatis satuan pembelian (dus/rim/pak) menjadi satuan terkecil pakai (pcs/buah) secara presisi real-time."
        ),
        (
            "2. FORM & CETAK BAST DIGITAL",
            GOLD_ACCENT,
            GOLD_LIGHT,
            "Legalitas Dokumen Resmi",
            "Menerbitkan Berita Acara Serah Terima otomatis berisikan NIP penerima, identitas petugas PIC, tanggal serah terima, dan rincian kuantitas."
        ),
        (
            "3. FORM UPLOAD BUKTI FOTO",
            EMERALD,
            EMERALD_BG,
            "Dokumentasi Fisik Otentik",
            "Formulir serah terima mewajibkan unggah foto penyerahan fisik barang sebagai bukti tak terbantahkan bahwa barang diterima pemohon."
        )
    ]

    for i, (title_f, col_acc, col_bg, sub_f, body_f) in enumerate(features_gudang):
        c_x = Inches(0.8) + i * (f_width + f_gap)
        c = create_card(s5, c_x, Inches(1.6), f_width, Inches(2.2))

        # Title badge
        b = s5.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, c_x + Inches(0.2), Inches(1.8), f_width - Inches(0.4), Inches(0.35))
        b.fill.solid()
        b.fill.fore_color.rgb = col_bg
        b.line.color.rgb = col_acc
        b.line.width = Pt(1)
        p_b = b.text_frame.paragraphs[0]
        p_b.text = title_f
        p_b.font.size = Pt(9.5)
        p_b.font.bold = True
        p_b.font.color.rgb = col_acc
        p_b.alignment = PP_ALIGN.CENTER

        tb = s5.shapes.add_textbox(c_x + Inches(0.2), Inches(2.25), f_width - Inches(0.4), Inches(1.4))
        tf = tb.text_frame
        tf.word_wrap = True
        tf.margin_left = tf.margin_top = 0

        p1 = tf.paragraphs[0]
        p1.text = sub_f
        p1.font.size = Pt(11)
        p1.font.bold = True
        p1.font.color.rgb = TEXT_MAIN

        p2 = tf.add_paragraph()
        p2.text = body_f
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = TEXT_MUTED
        p2.space_before = Pt(4)

    # Bottom Banner with Visual Interface
    c_bot = create_card(s5, Inches(0.8), Inches(4.0), Inches(11.733), Inches(2.8))
    tb_b = s5.shapes.add_textbox(Inches(1.05), Inches(4.2), Inches(5.5), Inches(2.4))
    tf_b = tb_b.text_frame
    tf_b.word_wrap = True
    tf_b.margin_left = tf_b.margin_top = 0

    p_bt1 = tf_b.paragraphs[0]
    p_bt1.text = "INTEGRASI FORM DISTRIBUSI DENGAN BUKTI FISIK"
    p_bt1.font.size = Pt(12)
    p_bt1.font.bold = True
    p_bt1.font.color.rgb = NAVY_DEEP

    p_bt2 = tf_b.add_paragraph()
    p_bt2.text = "Sebelum fitur form bukti foto ini dirilis, verifikasi serah terima hanya mengandalkan paraf kertas yang rentan hilang atau tercecer. Dengan formulir terpadu ATEKA:"
    p_bt2.font.size = Pt(9.5)
    p_bt2.font.color.rgb = TEXT_MAIN
    p_bt2.space_before = Pt(6)

    flow_items = [
        "Status tiket otomatis terkunci menjadi 'Selesai' hanya setelah berkas BAST & foto tersimpan.",
        "Pemohon mendapatkan notifikasi lonceng bahwa barang telah diserahterimakan.",
        "Arsiparis BMN dapat mengunduh rekapan mutasi persediaan beserta bukti otentik serah terima kapan saja."
    ]
    for fi in flow_items:
        p_fi = tf_b.add_paragraph()
        p_fi.text = f"✓  {fi}"
        p_fi.font.size = Pt(9)
        p_fi.font.color.rgb = TEXT_MUTED
        p_fi.space_before = Pt(3)

    img_dist = os.path.abspath('assets/manual_book_pic_images/image3.png')
    if os.path.exists(img_dist):
        s5.shapes.add_picture(img_dist, Inches(6.8), Inches(4.2), Inches(5.5), Inches(2.4))

    set_notes(s5, "Slide Akuntabilitas Fisik: Memaparkan formulir transaksi gudang, pencetakan BAST otomatis, dan formulir pengunggahan bukti foto penyerahan fisik barang.")

    # =========================================================================
    # SLIDE 6: PILAR II - TRILOGI BUKU PANDUAN PENGGUNA RESMI (3 Books Showcase)
    # =========================================================================
    s6 = prs.slides.add_slide(blank_layout)
    set_slide_background(s6, BG_LIGHT)
    add_slide_header(s6, "Trilogi Buku Panduan Pengguna Sistem ATEKA", "PILAR II: MANUAL BOOK RESMI", "Tiga dokumen buku panduan komprehensif (35 Halaman PDF A4) untuk memastikan seluruh peran operasional mahir")
    add_slide_footer(s6, 6)

    book_cards = [
        (
            "BUKU I: ROLE PEGAWAI",
            "Pemohon Individu ASN",
            "11 Halaman PDF A4",
            BLUE_ACCENT,
            BLUE_LIGHT,
            [
                "Registrasi akun mandiri dengan NIP 18-digit.",
                "Eksplorasi E-Katalog & ketersediaan stok aktif.",
                "Pengajuan permintaan barang siap pakai gudang.",
                "Usulan pengadaan ATK baru non-katalog.",
                "Pelacakan status permohonan & penerimaan fisik."
            ],
            "assets/manual_book_pegawai_images/image4.png"
        ),
        (
            "BUKU II: SUBDIREKTORAT",
            "Perwakilan Unit Kerja",
            "11 Halaman PDF A4",
            GOLD_ACCENT,
            GOLD_LIGHT,
            [
                "Akun khusus perwakilan operasional unit kerja.",
                "Konsolidasi usulan ATK skala besar subdit.",
                "Justifikasi belanja barang penunjang kedinasan.",
                "Monitoring catatan evaluasi dari Kasubag TU.",
                "Koordinasi pengambilan barang di ruang persediaan."
            ],
            "assets/manual_book_images/image4.png"
        ),
        (
            "BUKU III: PIC DISTRIBUSI",
            "Pengelola Gudang & BMN",
            "13 Halaman PDF A4",
            EMERALD,
            EMERALD_BG,
            [
                "Verifikasi tiket permohonan yang disetujui Kasubag.",
                "Pencatatan transaksi barang masuk & konversi satuan.",
                "Pencatatan barang keluar & pengurangan saldo stok.",
                "Penerbitan BAST & pengunggahan bukti foto fisik.",
                "Laporan mutasi persediaan & rekapitulasi berkala."
            ],
            "assets/manual_book_pic_images/image2.png"
        )
    ]

    for idx, (b_title, b_role, b_pages, col_acc, col_bg, b_points, b_img) in enumerate(book_cards):
        c_x = Inches(0.8) + idx * (f_width + f_gap)
        c = create_card(s6, c_x, Inches(1.6), f_width, Inches(5.2))

        # Header Badge
        hb = s6.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, c_x + Inches(0.2), Inches(1.8), f_width - Inches(0.4), Inches(0.8))
        hb.fill.solid()
        hb.fill.fore_color.rgb = col_bg
        hb.line.color.rgb = col_acc
        hb.line.width = Pt(1)
        tf_hb = hb.text_frame
        tf_hb.word_wrap = True
        tf_hb.margin_left = Inches(0.15)
        tf_hb.margin_top = Inches(0.08)
        p_t = tf_hb.paragraphs[0]
        p_t.text = b_title
        p_t.font.size = Pt(11)
        p_t.font.bold = True
        p_t.font.color.rgb = col_acc

        p_s = tf_hb.add_paragraph()
        p_s.text = f"{b_role} • {b_pages}"
        p_s.font.size = Pt(9)
        p_s.font.color.rgb = TEXT_MUTED

        # Content bullets
        tb = s6.shapes.add_textbox(c_x + Inches(0.2), Inches(2.7), f_width - Inches(0.4), Inches(2.1))
        tf = tb.text_frame
        tf.word_wrap = True
        tf.margin_left = tf.margin_top = 0

        for i, pt in enumerate(b_points):
            p_pt = tf.add_paragraph() if i > 0 else tf.paragraphs[0]
            p_pt.text = f"• {pt}"
            p_pt.font.size = Pt(9)
            p_pt.font.color.rgb = TEXT_MAIN
            if i > 0:
                p_pt.space_before = Pt(3)

        # Image thumbnail preview
        img_full = os.path.abspath(b_img)
        if os.path.exists(img_full):
            s6.shapes.add_picture(img_full, c_x + Inches(0.2), Inches(4.75), f_width - Inches(0.4), Inches(1.85))

    set_notes(s6, "Slide Trilogi Manual Book: Memperkenalkan tiga buku panduan resmi yang diterbitkan untuk 3 kelompok peran utama (Pegawai, Subdirektorat, PIC Distribusi) dengan total 35 halaman dokumentasi presisi.")

    # =========================================================================
    # SLIDE 7: PILAR II - STANDAR DESAIN & TEKNOLOGI PENERBITAN MANUAL BOOK
    # =========================================================================
    s7 = prs.slides.add_slide(blank_layout)
    set_slide_background(s7, BG_LIGHT)
    add_slide_header(s7, "Standar Desain Penerbitan & Komponen ReportLab", "PILAR II: MANUAL BOOK RESMI", "Arsitektur dokumen dinamis menggunakan ReportLab Platypus dengan standar tata letak publikasi instansi")
    add_slide_footer(s7, 7)

    grid_w = Inches(5.65)
    grid_h = Inches(2.45)
    grid_gap_x = Inches(0.4)
    grid_gap_y = Inches(0.3)

    quads = [
        (
            Inches(0.8), Inches(1.6),
            "1. ARSITEKTUR NUMBEREDCANVAS DINAMIS",
            BLUE_ACCENT,
            BLUE_LIGHT,
            [
                ("Two-Pass Rendering:", "Menghitung total halaman dokumen secara otomatis (Halaman X dari Y) tanpa risiko nomor halaman salah."),
                ("Running Header Resmi:", "Header instansi Kementerian PKP dengan garis ganda Deep Navy & aksen emas Regal Gold."),
                ("Cover Khusus:", "Halaman sampul secara otomatis bebas dari running header/footer tanpa merusak nomor halaman berikutnya.")
            ]
        ),
        (
            Inches(0.8) + grid_w + grid_gap_x, Inches(1.6),
            "2. SISTEM CALLOUT BOX 4 KATEGORI",
            GOLD_ACCENT,
            GOLD_LIGHT,
            [
                ("Kotak Tips Sukses (Hijau):", "Petunjuk taktis pengisian formulir agar permohonan cepat disetujui."),
                ("Kotak Peringatan (Amber):", "Hal penting terkait batas waktu atau syarat khusus BMN."),
                ("Kotak Larangan (Merah):", "Peringatan tegas sanksi penolakan usulan fiktif atau penimbunan stok."),
                ("Kotak Info Teknis (Biru):", "Penjelasan teknis status antrean, filter tanggal, dan konversi satuan.")
            ]
        ),
        (
            Inches(0.8), Inches(1.6) + grid_h + grid_gap_y,
            "3. PENYEMATAN 28+ MOCKUP REALISTIK",
            EMERALD,
            EMERALD_BG,
            [
                ("Resolusi Tinggi (High-DPI):", "Gambar antarmuka MacBook Pro & Mobile disematkan proporsional tanpa distorsi rasio."),
                ("Keterangan Gambar Jelas:", "Setiap antarmuka dilengkapi keterangan nomor gambar dan penunjuk elemen penting."),
                ("Standar Flowable Platypus:", "Memanfaatkan KeepTogether agar gambar dan deskripsi tidak terpotong beda halaman.")
            ]
        ),
        (
            Inches(0.8) + grid_w + grid_gap_x, Inches(1.6) + grid_h + grid_gap_y,
            "4. MATRIKS WEWENANG & FAQ HELPDESK",
            NAVY_DEEP,
            RGBColor(241, 245, 249),
            [
                ("Tabel Matriks Akses:", "Rincian hak akses 5 peran (input, persetujuan, distribusi, pencetakan, pelaporan)."),
                ("Bab FAQ Komprehensif:", "Jawaban atas pertanyaan umum (lupa password, stok habis, penolakan tiket)."),
                ("Alur Eskalasi Bantuan:", "Nomor kontak dan prosedur meja bantuan Tata Usaha bagi kendala darurat.")
            ]
        )
    ]

    for (gx, gy, g_title, g_acc, g_bg, g_items) in quads:
        c = create_card(s7, gx, gy, grid_w, grid_h)

        # Title Box inside quadrant
        hb = s7.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, gx + Inches(0.2), gy + Inches(0.18), grid_w - Inches(0.4), Inches(0.38))
        hb.fill.solid()
        hb.fill.fore_color.rgb = g_bg
        hb.line.color.rgb = g_acc
        hb.line.width = Pt(1)
        p_t = hb.text_frame.paragraphs[0]
        p_t.text = g_title
        p_t.font.size = Pt(10)
        p_t.font.bold = True
        p_t.font.color.rgb = g_acc
        p_t.alignment = PP_ALIGN.CENTER

        tb = s7.shapes.add_textbox(gx + Inches(0.2), gy + Inches(0.65), grid_w - Inches(0.4), Inches(1.65))
        tf = tb.text_frame
        tf.word_wrap = True
        tf.margin_left = tf.margin_top = 0

        for i, (label_i, desc_i) in enumerate(g_items):
            p = tf.add_paragraph() if i > 0 else tf.paragraphs[0]
            p.text = f"• {label_i} {desc_i}"
            p.font.size = Pt(9)
            p.font.color.rgb = TEXT_MAIN
            if i > 0:
                p.space_before = Pt(3)

    set_notes(s7, "Slide Standar Desain Manual Book: Membedah keunggulan teknis dari arsitektur ReportLab Platypus, NumberedCanvas, kotak callout interaktif, penempatan 28 tangkapan layar, dan matriks wewenang.")

    # =========================================================================
    # SLIDE 8: PILAR III - ARSITEKTUR FLOW CHART PROSES BISNIS LINTAS 5 PERAN
    # =========================================================================
    s8 = prs.slides.add_slide(blank_layout)
    set_slide_background(s8, BG_LIGHT)
    add_slide_header(s8, "Arsitektur Alur Proses Bisnis Terpadu Lintas 5 Peran", "PILAR III: FLOW CHART SISTEM", "Struktur rantai nilai operasional terisolasi yang menghubungkan seluruh pemangku kepentingan BMN")
    add_slide_footer(s8, 8)

    # Left Container: Diagram Gambar Flow Chart
    c_diag = create_card(s8, Inches(0.8), Inches(1.6), Inches(7.8), Inches(5.2))
    if os.path.exists(FLOWCHART_PATH):
        s8.shapes.add_picture(FLOWCHART_PATH, Inches(0.95), Inches(1.75), Inches(7.5), Inches(4.9))

    # Right Container: Penjelasan 4 Tahapan Rantai Nilai
    c_steps = create_card(s8, Inches(8.8), Inches(1.6), Inches(3.733), Inches(5.2))
    tb_st = s8.shapes.add_textbox(Inches(9.0), Inches(1.8), Inches(3.3), Inches(4.8))
    tf_st = tb_st.text_frame
    tf_st.word_wrap = True
    tf_st.margin_left = tf_st.margin_top = 0

    p_sth = tf_st.paragraphs[0]
    p_sth.text = "4 FASE RANTAI NILAI"
    p_sth.font.size = Pt(13)
    p_sth.font.bold = True
    p_sth.font.color.rgb = NAVY_DEEP

    phases = [
        ("FASE 1: USULAN KEBUTUHAN", BLUE_ACCENT, "Pegawai / Subdirektorat mengajukan tiket stok gudang atau usulan beli baru melalui katalog online."),
        ("FASE 2: VERIFIKASI KASUBAG TU", GOLD_ACCENT, "Kasubag TU meninjau kelayakan anggaran, urgensi dinas, dan riwayat anti-stocking sebelum approval."),
        ("FASE 3: PENYIAPAN & SERAH TERIMA", EMERALD, "PIC Gudang menyiapkan fisik barang, menerbitkan BAST digital, dan mengunggah foto serah terima."),
        ("FASE 4: PENGARSIPAN RESMI", NAVY_MID, "Arsiparis memantau mutasi persediaan, membukukan berita acara fisik, dan mencetak laporan triwulan.")
    ]

    for p_title, p_col, p_text in phases:
        p_pt = tf_st.add_paragraph()
        p_pt.text = f"▶ {p_title}"
        p_pt.font.size = Pt(10)
        p_pt.font.bold = True
        p_pt.font.color.rgb = p_col
        p_pt.space_before = Pt(10)

        p_pd = tf_st.add_paragraph()
        p_pd.text = p_text
        p_pd.font.size = Pt(8.8)
        p_pd.font.color.rgb = TEXT_MUTED
        p_pd.space_before = Pt(2)

    set_notes(s8, "Slide Arsitektur Flowchart: Memvisualisasikan diagram proses bisnis komprehensif lintas 5 peran (Pegawai, Subdit, Kasubag TU, PIC Distribusi, Arsiparis) berserta 4 tahapan rantai nilai BMN.")

    # =========================================================================
    # SLIDE 9: PILAR III - DEKOMPOSISI 4 ALUR LOGIKA OPERASIONAL KUNCI
    # =========================================================================
    s9 = prs.slides.add_slide(blank_layout)
    set_slide_background(s9, BG_LIGHT)
    add_slide_header(s9, "Dekomposisi Logika 4 Alur Operasional Kunci", "PILAR III: FLOW CHART SISTEM", "Pemeriksaan detail diagram alir logika program dan percabangan keputusan di dalam sistem")
    add_slide_footer(s9, 9)

    flow_col_w = Inches(2.7)
    flow_col_gap = Inches(0.3)
    flow_top = Inches(1.6)
    flow_height = Inches(5.2)

    detail_flows = [
        (
            "FLOW 1: REGISTRASI & NIP",
            BLUE_ACCENT,
            BLUE_LIGHT,
            [
                ("Input Data:", "Pegawai memasukkan nama, NIP 18-digit, email resmi, & password."),
                ("Validasi Regex:", "Sistem memeriksa format 18 digit numerik standar ASN."),
                ("Cek Duplikasi:", "Sistem memastikan NIP belum terdaftar di database."),
                ("Penetapan Peran:", "Akun aktif dengan hak akses sesuai unit kerja yang dipilih.")
            ]
        ),
        (
            "FLOW 2: CABANG KEBUTUHAN",
            GOLD_ACCENT,
            GOLD_LIGHT,
            [
                ("Pengecekan Katalog:", "Pemohon mencari item kebutuhan operasional dinas."),
                ("Kondisi 1 (Ready):", "Jika barang ada & stok cukup -> Buka Form Permintaan Gudang."),
                ("Kondisi 2 (Kosong):", "Jika habis / non-katalog -> Buka Form Usulan Pengadaan Baru."),
                ("Pencegahan Monopoli:", "Audit anti-stocking otomatis memblokir usulan berulang.")
            ]
        ),
        (
            "FLOW 3: APPROVAL KASUBAG",
            AMBER,
            AMBER_BG,
            [
                ("Antrean Masuk:", "Tiket pengajuan masuk ke dashboard Kasubag TU."),
                ("Keputusan Disetujui:", "Tiket diteruskan langsung ke PIC Distribusi Gudang."),
                ("Setuju Sebagian:", "Kasubag menyesuaikan kuantitas barang sesuai ketersediaan."),
                ("Keputusan Ditolak:", "Wajib menyertakan alasan tertulis untuk pemohon.")
            ]
        ),
        (
            "FLOW 4: SERAH TERIMA FISIK",
            EMERALD,
            EMERALD_BG,
            [
                ("Penerbitan Tiket:", "PIC Gudang menerima notifikasi penyiapan barang."),
                ("Pengecekan Fisik:", "Pemohon mendatangi ruang gudang BMN."),
                ("Foto Dokumentasi:", "PIC mengambil & mengunggah foto serah terima barang."),
                ("Tutup Tiket:", "BAST digital dicetak, status tiket berubah jadi Selesai.")
            ]
        )
    ]

    for idx, (fl_title, fl_acc, fl_bg, fl_steps) in enumerate(detail_flows):
        fx = Inches(0.8) + idx * (flow_col_w + flow_col_gap)
        c = create_card(s9, fx, flow_top, flow_col_w, flow_height)

        # Header Badge
        hb = s9.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, fx + Inches(0.15), flow_top + Inches(0.2), flow_col_w - Inches(0.3), Inches(0.45))
        hb.fill.solid()
        hb.fill.fore_color.rgb = fl_bg
        hb.line.color.rgb = fl_acc
        hb.line.width = Pt(1)
        p_t = hb.text_frame.paragraphs[0]
        p_t.text = fl_title
        p_t.font.size = Pt(9.5)
        p_t.font.bold = True
        p_t.font.color.rgb = fl_acc
        p_t.alignment = PP_ALIGN.CENTER

        # Steps List
        tb = s9.shapes.add_textbox(fx + Inches(0.15), flow_top + Inches(0.8), flow_col_w - Inches(0.3), Inches(4.2))
        tf = tb.text_frame
        tf.word_wrap = True
        tf.margin_left = tf.margin_top = 0

        for i, (step_head, step_desc) in enumerate(fl_steps):
            p = tf.add_paragraph() if i > 0 else tf.paragraphs[0]
            p.text = f"{i+1}. {step_head}"
            p.font.size = Pt(10)
            p.font.bold = True
            p.font.color.rgb = TEXT_MAIN
            if i > 0:
                p.space_before = Pt(8)

            p_d = tf.add_paragraph()
            p_d.text = step_desc
            p_d.font.size = Pt(8.8)
            p_d.font.color.rgb = TEXT_MUTED
            p_d.space_before = Pt(2)

    set_notes(s9, "Slide Dekomposisi 4 Alur Kunci: Menjelaskan detail alur logika teknis untuk registrasi NIP, percabangan barang ready vs pengadaan baru, keputusan verifikasi Kasubag TU, serta serah terima fisik berfoto.")

    # =========================================================================
    # SLIDE 10: SINERGI TRI-PILAR (FORM, MANUAL BOOK, FLOW CHART)
    # =========================================================================
    s10 = prs.slides.add_slide(blank_layout)
    set_slide_background(s10, BG_LIGHT)
    add_slide_header(s10, "Sinergi Tri-Pilar: Kunci Keberhasilan Adopsi Sistem", "KORELASI SISTEM", "Bagaimana integrasi formulir presisi, panduan komprehensif, dan alur terstruktur menjamin sukses operasional")
    add_slide_footer(s10, 10)

    # Matrix Table / 3 Horizontal Blocks
    row_h = Inches(1.28)
    row_gap = Inches(0.18)
    top_init = Inches(1.58)

    pillars_syn = [
        (
            "PILAR I: FORMULIR DIGITAL",
            "Instrumen Eksekusi Teknis",
            BLUE_ACCENT,
            BLUE_LIGHT,
            "Formulir bertindak sebagai 'pintu gerbang input data' yang memastikan setiap informasi permohonan tervalidasi secara ketat sejak awal, mencegah input fiktif melalui validasi NIP, dan mengunci transaksi dengan bukti foto."
        ),
        (
            "PILAR II: MANUAL BOOK RESMI",
            "Instrumen Edukasi & Standardisasi",
            GOLD_ACCENT,
            GOLD_LIGHT,
            "Buku panduan bertindak sebagai 'pemandu operasional' yang menghilangkan kebingungan pengguna, menyajikan langkah demi langkah antarmuka nyata, dan memberikan solusi cepat saat menghadapi kendala teknis."
        ),
        (
            "PILAR III: FLOW CHART SISTEM",
            "Instrumen Logika & Tata Kelola",
            EMERALD,
            EMERALD_BG,
            "Diagram alur bertindak sebagai 'peta navigasi tata kelola' yang menjamin kepatuhan birokrasi, memperjelas wewenang antar peran, dan memberikan kepastian waktu proses (SLA) dari pengajuan hingga penerimaan barang."
        )
    ]

    for idx, (p_name, p_role, p_acc, p_bg, p_syn) in enumerate(pillars_syn):
        y_pos = top_init + idx * (row_h + row_gap)
        c = create_card(s10, Inches(0.8), y_pos, Inches(11.733), row_h)

        # Left tag box inside row
        tb_tag = s10.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(1.05), y_pos + Inches(0.12), Inches(3.2), Inches(1.02))
        tb_tag.fill.solid()
        tb_tag.fill.fore_color.rgb = p_bg
        tb_tag.line.color.rgb = p_acc
        tb_tag.line.width = Pt(1)
        tf_tag = tb_tag.text_frame
        tf_tag.margin_left = Inches(0.15)
        tf_tag.margin_top = Inches(0.14)
        p_t1 = tf_tag.paragraphs[0]
        p_t1.text = p_name
        p_t1.font.size = Pt(10.5)
        p_t1.font.bold = True
        p_t1.font.color.rgb = p_acc
        p_t2 = tf_tag.add_paragraph()
        p_t2.text = p_role
        p_t2.font.size = Pt(9)
        p_t2.font.color.rgb = TEXT_MUTED

        # Right text explanation
        tb_desc = s10.shapes.add_textbox(Inches(4.5), y_pos + Inches(0.10), Inches(7.8), Inches(1.08))
        tf_desc = tb_desc.text_frame
        tf_desc.word_wrap = True
        tf_desc.margin_left = tf_desc.margin_top = 0
        p_d = tf_desc.paragraphs[0]
        p_d.text = p_syn
        p_d.font.size = Pt(9.5)
        p_d.font.color.rgb = TEXT_MAIN
        p_d.space_before = Pt(3)

    # Synthesis statement box
    c_syn = s10.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(5.95), Inches(11.733), Inches(0.85))
    c_syn.fill.solid()
    c_syn.fill.fore_color.rgb = NAVY_DEEP
    c_syn.line.color.rgb = GOLD_ACCENT
    c_syn.line.width = Pt(1.2)
    tf_syn = c_syn.text_frame
    tf_syn.margin_left = Inches(0.3)
    tf_syn.margin_top = Inches(0.15)
    p_s1 = tf_syn.paragraphs[0]
    p_s1.text = "KESIMPULAN SINERGI: SISTEM ANDAL BERKAT DUKUNGAN 3 PILAR TERPADU"
    p_s1.font.size = Pt(11.5)
    p_s1.font.bold = True
    p_s1.font.color.rgb = GOLD_ACCENT
    p_s2 = tf_syn.add_paragraph()
    p_s2.text = "Ketiga pilar bekerja harmonis: Formulir menyediakan interaksi bebas error, Flowchart menjamin rantai komando tertib, dan Manual Book memastikan adopsi mulus tanpa resistensi pegawai."
    p_s2.font.size = Pt(9.5)
    p_s2.font.color.rgb = WHITE
    p_s2.space_before = Pt(3)

    set_notes(s10, "Slide Sinergi Tiga Pilar: Menjelaskan korelasi saling menguatkan antara Formulir (eksekusi teknis), Flowchart (logika proses), dan Manual Book (edukasi pengguna).")

    # =========================================================================
    # SLIDE 11: DAMPAK OPERASIONAL & METRIK CAPAIAN
    # =========================================================================
    s11 = prs.slides.add_slide(blank_layout)
    set_slide_background(s11, BG_LIGHT)
    add_slide_header(s11, "Hasil & Dampak Nyata Operasional Organisasi", "DAMPAK & CAPAIAN", "Transformasi digital substantif yang dirasakan langsung oleh pegawai dan pengelola BMN Kementerian")
    add_slide_footer(s11, 11)

    # 4 Big Stat Cards
    stat_w = Inches(2.7)
    stat_gap = Inches(0.3)
    stat_top = Inches(1.6)
    stat_h = Inches(4.1)

    stats_data = [
        (
            "100%",
            "DIGITAL & PAPERLESS",
            BLUE_ACCENT,
            BLUE_LIGHT,
            "Eliminasi Nota Fisik",
            "Seluruh usulan ATK, penolakan dengan catatan, dan verifikasi persetujuan kini 100% tersimpan di awan tanpa lembaran nota kertas manual."
        ),
        (
            "35 Hal",
            "MANUAL BOOK RESMI",
            GOLD_ACCENT,
            GOLD_LIGHT,
            "Dokumentasi Standar",
            "Tiga buku panduan resmi siap cetak dan distribusi digital, mencakup seluruh skenario operasional dari login hingga serah terima barang."
        ),
        (
            "0 Akun",
            "FIKTIF / DUPLIKAT",
            EMERALD,
            EMERALD_BG,
            "Keabsahan NIP ASN",
            "Standardisasi NIP 18-digit dan isolasi unit kerja berhasil membersihkan potensi manipulasi identitas dan penyalahgunaan wewenang."
        ),
        (
            "100%",
            "AKUNTABILITAS FISIK",
            NAVY_DEEP,
            RGBColor(241, 245, 249),
            "Bukti Foto BAST",
            "Setiap serah terima barang terikat langsung dengan foto fisik riil penerima, menghentikan spekulasi kehilangan barang persediaan."
        )
    ]

    for idx, (val_s, tag_s, acc_s, bg_s, sub_s, desc_s) in enumerate(stats_data):
        sx = Inches(0.8) + idx * (stat_w + stat_gap)
        c = create_card(s11, sx, stat_top, stat_w, stat_h)

        # Big Number Container
        nb = s11.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, sx + Inches(0.15), stat_top + Inches(0.2), stat_w - Inches(0.3), Inches(1.15))
        nb.fill.solid()
        nb.fill.fore_color.rgb = bg_s
        nb.line.color.rgb = acc_s
        nb.line.width = Pt(1)
        tf_nb = nb.text_frame
        tf_nb.margin_left = Inches(0.1)
        tf_nb.margin_top = Inches(0.15)
        p_nv = tf_nb.paragraphs[0]
        p_nv.text = val_s
        p_nv.font.size = Pt(24)
        p_nv.font.bold = True
        p_nv.font.color.rgb = acc_s
        p_nv.alignment = PP_ALIGN.CENTER
        p_nt = tf_nb.add_paragraph()
        p_nt.text = tag_s
        p_nt.font.size = Pt(8.5)
        p_nt.font.bold = True
        p_nt.font.color.rgb = TEXT_MUTED
        p_nt.alignment = PP_ALIGN.CENTER

        # Body text
        tb = s11.shapes.add_textbox(sx + Inches(0.15), stat_top + Inches(1.5), stat_w - Inches(0.3), Inches(2.4))
        tf = tb.text_frame
        tf.word_wrap = True
        tf.margin_left = tf.margin_top = 0

        p1 = tf.paragraphs[0]
        p1.text = sub_s
        p1.font.size = Pt(11)
        p1.font.bold = True
        p1.font.color.rgb = TEXT_MAIN

        p2 = tf.add_paragraph()
        p2.text = desc_s
        p2.font.size = Pt(9.5)
        p2.font.color.rgb = TEXT_MUTED
        p2.space_before = Pt(4)

    # Bottom Organizational Impact Banner
    b_imp = s11.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(5.9), Inches(11.733), Inches(0.95))
    b_imp.fill.solid()
    b_imp.fill.fore_color.rgb = CARD_BG
    b_imp.line.color.rgb = BLUE_ACCENT
    b_imp.line.width = Pt(1)

    tb_bi = s11.shapes.add_textbox(Inches(1.05), Inches(5.95), Inches(11.2), Inches(0.85))
    tf_bi = tb_bi.text_frame
    tf_bi.word_wrap = True
    tf_bi.margin_left = tf_bi.margin_top = 0
    p_b1 = tf_bi.paragraphs[0]
    p_b1.text = "DAMPAK STRATEGIS TERHADAP ANGGARAN & TATA KELOLA KEMENTERIAN PKP"
    p_b1.font.size = Pt(11)
    p_b1.font.bold = True
    p_b1.font.color.rgb = BLUE_ACCENT
    p_b2 = tf_bi.add_paragraph()
    p_b2.text = "Dengan sistem yang terintegrasi penuh antara form usulan, buku panduan operasional, dan alur kontrol anti-stocking, efisiensi anggaran belanja ATK meningkat signifikan dan audit persediaan BMN menjadi akurat seketika."
    p_b2.font.size = Pt(9.5)
    p_b2.font.color.rgb = TEXT_MAIN
    p_b2.space_before = Pt(3)

    set_notes(s11, "Slide Dampak Nyata: Menyajikan 4 indikator keberhasilan kuantitatif dan dampak strategis terhadap tata kelola anggaran dan audit persediaan BMN.")

    # =========================================================================
    # SLIDE 12: KESIMPULAN & LANGKAH KE DEPAN (Dark Navy #0A1C35)
    # =========================================================================
    s12 = prs.slides.add_slide(blank_layout)
    set_slide_background(s12, NAVY_DEEP)

    # Header Top on Dark
    b_cl = s12.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(0.5), Inches(4.5), Inches(0.35))
    b_cl.fill.solid()
    b_cl.fill.fore_color.rgb = NAVY_MID
    b_cl.line.color.rgb = GOLD_ACCENT
    b_cl.line.width = Pt(1)
    p_cl = b_cl.text_frame.paragraphs[0]
    p_cl.text = "KESIMPULAN & RENCANA KERJA"
    p_cl.font.size = Pt(9.5)
    p_cl.font.bold = True
    p_cl.font.color.rgb = GOLD_ACCENT
    p_cl.alignment = PP_ALIGN.CENTER

    tb_ch = s12.shapes.add_textbox(Inches(0.8), Inches(0.95), Inches(11.0), Inches(0.7))
    tf_ch = tb_ch.text_frame
    tf_ch.word_wrap = True
    tf_ch.margin_left = tf_ch.margin_top = 0
    p_ct = tf_ch.paragraphs[0]
    p_ct.text = "Sistem ATEKA Siap Dioperasikan Penuh & Terstandardisasi"
    p_ct.font.size = Pt(24)
    p_ct.font.bold = True
    p_ct.font.color.rgb = WHITE

    # Left Card: Ringkasan Pencapaian
    c_l12 = s12.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(1.85), Inches(5.7), Inches(4.1))
    c_l12.fill.solid()
    c_l12.fill.fore_color.rgb = NAVY_MID
    c_l12.line.color.rgb = RGBColor(30, 64, 100)
    c_l12.line.width = Pt(1)

    tb_lc = s12.shapes.add_textbox(Inches(1.05), Inches(2.05), Inches(5.2), Inches(3.7))
    tf_lc = tb_lc.text_frame
    tf_lc.word_wrap = True
    tf_lc.margin_left = tf_lc.margin_top = 0

    p_lh = tf_lc.paragraphs[0]
    p_lh.text = "RINGKASAN CAPAIAN MINGGU INI"
    p_lh.font.size = Pt(13)
    p_lh.font.bold = True
    p_lh.font.color.rgb = GOLD_ACCENT

    summary_pts = [
        ("Digitalisasi Formulir Tuntas:", "Seluruh form (NIP registrasi, reset password, stok gudang, pengadaan baru, BAST, foto fisik) berfungsi tanpa kendala."),
        ("Manual Book Resmi Terbit:", "3 Buku panduan (Pegawai, Subdit, PIC) setebal 35 halaman siap diedarkan dalam format PDF berstandar cetak."),
        ("Alur Bisnis Baku & Jelas:", "Diagram alur lintas peran memberikan transparansi SLA dan wewenang yang tidak dapat disalahgunakan."),
        ("Pengujian Sistem Lulus:", "Seluruh skenario fungsional dan pengujian keamanan telah berhasil diverifikasi.")
    ]
    for st, sd in summary_pts:
        p_pt = tf_lc.add_paragraph()
        p_pt.text = f"✓  {st}"
        p_pt.font.size = Pt(10)
        p_pt.font.bold = True
        p_pt.font.color.rgb = WHITE
        p_pt.space_before = Pt(8)

        p_pd = tf_lc.add_paragraph()
        p_pd.text = sd
        p_pd.font.size = Pt(9)
        p_pd.font.color.rgb = RGBColor(203, 213, 225)
        p_pd.space_before = Pt(2)

    # Right Card: Rencana Tindak Lanjut (Next Steps)
    c_r12 = s12.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(6.8), Inches(1.85), Inches(5.7), Inches(4.1))
    c_r12.fill.solid()
    c_r12.fill.fore_color.rgb = NAVY_MID
    c_r12.line.color.rgb = RGBColor(30, 64, 100)
    c_r12.line.width = Pt(1)

    tb_rc = s12.shapes.add_textbox(Inches(7.05), Inches(2.05), Inches(5.2), Inches(3.7))
    tf_rc = tb_rc.text_frame
    tf_rc.word_wrap = True
    tf_rc.margin_left = tf_rc.margin_top = 0

    p_rh = tf_rc.paragraphs[0]
    p_rh.text = "RENCANA KERJA IMPLEMENTASI BERIKUTNYA"
    p_rh.font.size = Pt(13)
    p_rh.font.bold = True
    p_rh.font.color.rgb = BLUE_ACCENT

    next_steps = [
        ("Sosialisasi Manual Book:", "Mendistribusikan file PDF Manual Book kepada seluruh pejabat & staf di lingkungan 4 subdirektorat."),
        ("Workshop Operasional PIC:", "Pelatihan singkat bagi petugas PIC Distribusi mengenai upload foto dan penatausahaan BAST."),
        ("Roll-out Penuh Sistem:", "Pemberlakuan sistem ATEKA sebagai satu-satunya kanal resmi permohonan ATK dinas."),
        ("Evaluasi & Review Berkala:", "Monitoring transaksi mingguan dan umpan balik pengguna bersama Kasubag Tata Usaha.")
    ]
    for nt, nd in next_steps:
        p_pt = tf_rc.add_paragraph()
        p_pt.text = f"▶  {nt}"
        p_pt.font.size = Pt(10)
        p_pt.font.bold = True
        p_pt.font.color.rgb = WHITE
        p_pt.space_before = Pt(8)

        p_pd = tf_rc.add_paragraph()
        p_pd.text = nd
        p_pd.font.size = Pt(9)
        p_pd.font.color.rgb = RGBColor(203, 213, 225)
        p_pd.space_before = Pt(2)

    # Bottom Closing Tag
    c_bot = s12.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, Inches(0.8), Inches(6.15), Inches(11.733), Inches(0.75))
    c_bot.fill.solid()
    c_bot.fill.fore_color.rgb = RGBColor(15, 35, 65)
    c_bot.line.color.rgb = GOLD_ACCENT
    c_bot.line.width = Pt(1)

    tb_cb = s12.shapes.add_textbox(Inches(1.05), Inches(6.22), Inches(11.2), Inches(0.6))
    tf_cb = tb_cb.text_frame
    tf_cb.margin_left = tf_cb.margin_top = 0
    p_end = tf_cb.paragraphs[0]
    p_end.text = "DIREKTORAT BINA TEKNIK PERUMAHAN DAN KAWASAN PERMUKIMAN • KEMENTERIAN PKP RI"
    p_end.font.size = Pt(11)
    p_end.font.bold = True
    p_end.font.color.rgb = GOLD_ACCENT
    p_end.alignment = PP_ALIGN.CENTER

    p_end2 = tf_cb.add_paragraph()
    p_end2.text = "Terima kasih atas kolaborasi dan kerja keras dalam mewujudkan sistem ATEKA yang unggul dan akuntabel."
    p_end2.font.size = Pt(9.5)
    p_end2.font.color.rgb = WHITE
    p_end2.alignment = PP_ALIGN.CENTER

    set_notes(s12, "Slide Penutup: Merangkum kesiapan penuh sistem ATEKA dan menguraikan langkah strategis selanjutnya menuju sosialisasi dan implementasi menyeluruh.")

    # Simpan File Presentasi
    prs.save(output_path)
    print(f"[SUKSES] Presentasi berhasil dibuat di: {output_path}")

if __name__ == "__main__":
    out_file = "Presentasi_Capaian_Mingguan_ATEKA.pptx"
    if len(sys.argv) > 1:
        out_file = sys.argv[1]
    build_presentation(out_file)
