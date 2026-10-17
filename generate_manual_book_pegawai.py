"""
Script Pembuat Buku Panduan Pengguna (User Manual Book)
Role Pegawai (Pemohon Individu) - Sistem ATEKA Kementerian PKP RI
Menggunakan ReportLab Platypus dengan Standar Desain Resmi & NumberedCanvas
Menyematkan seluruh 9 tangkapan layar antarmuka sistem secara proporsional.
"""

import os
import sys
from reportlab.lib.pagesizes import A4
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate,
    Paragraph,
    Spacer,
    Table,
    TableStyle,
    PageBreak,
    Image,
    KeepTogether,
    HRFlowable,
)
from reportlab.pdfgen import canvas

# ==============================================================================
# 1. NUMBERED CANVAS PATTERN (Two-pass dynamic page numbering & Running Headers)
# ==============================================================================
class AtekaNumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super().showPage()
        super().save()

    def draw_page_decorations(self, total_pages):
        # Halaman cover (halaman 1) tidak menampilkan running header & footer
        if self._pageNumber > 1:
            self.saveState()
            
            # --- RUNNING HEADER ---
            self.setFont("Helvetica-Bold", 7.5)
            self.setFillColor(colors.HexColor("#0F2D54")) # Deep Navy
            self.drawString(45, 842 - 30, "KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN RI")
            
            self.setFont("Helvetica", 7.5)
            self.setFillColor(colors.HexColor("#64748B")) # Slate Gray
            self.drawRightString(595 - 45, 842 - 30, "PANDUAN ATEKA • ROLE PEGAWAI (INDIVIDU)")
            
            # Garis pembatas Header (Navy + Aksen Gold)
            self.setStrokeColor(colors.HexColor("#E2E8F0"))
            self.setLineWidth(0.75)
            self.line(45, 842 - 36, 595 - 45, 842 - 36)
            
            self.setStrokeColor(colors.HexColor("#C59B27")) # Regal Gold Accent
            self.setLineWidth(1.5)
            self.line(45, 842 - 36, 45 + 100, 842 - 36)

            # --- RUNNING FOOTER ---
            self.setStrokeColor(colors.HexColor("#E2E8F0"))
            self.setLineWidth(0.75)
            self.line(45, 36, 595 - 45, 36)

            self.setFont("Helvetica", 7.2)
            self.setFillColor(colors.HexColor("#64748B"))
            self.drawString(45, 25, "Buku Panduan Pegawai (Pemohon Individu) • Edisi Terintegrasi 2026")
            
            page_text = f"Halaman {self._pageNumber} dari {total_pages}"
            self.setFont("Helvetica-Bold", 7.5)
            self.setFillColor(colors.HexColor("#0F2D54"))
            self.drawRightString(595 - 45, 25, page_text)
            
            self.restoreState()


# ==============================================================================
# 2. DEFINISI PALET WARNA & STYLESHEET RESMI
# ==============================================================================
C_NAVY_DARK    = colors.HexColor("#0A1C35")  # Navy Utama Kemen PKP
C_NAVY_MID     = colors.HexColor("#0F2D54")  # Navy Sekunder
C_NAVY_BLUE    = colors.HexColor("#0284C7")  # Cyan / Biru Aksen
C_GOLD_ACCENT  = colors.HexColor("#C59B27")  # Emas Resmi
C_GOLD_LIGHT   = colors.HexColor("#FEF9C3")  # Latar Emas Lembut
C_BG_LIGHT     = colors.HexColor("#F8FAFC")  # Ice Gray Latar Kartu
C_BORDER_GRAY  = colors.HexColor("#CBD5E1")  # Garis Pembatas
C_TEXT_MAIN    = colors.HexColor("#0F172A")  # Hitam Teks Utama
C_TEXT_MUTED   = colors.HexColor("#475569")  # Abu-abu Deskripsi
C_EMERALD      = colors.HexColor("#059669")  # Hijau Sukses
C_EMERALD_BG   = colors.HexColor("#ECFDF5")  # Latar Hijau Sukses
C_AMBER        = colors.HexColor("#D97706")  # Peringatan Amber
C_AMBER_BG     = colors.HexColor("#FFFBEB")  # Latar Peringatan Amber
C_ROSE         = colors.HexColor("#E11D48")  # Merah Danger
C_ROSE_BG      = colors.HexColor("#FFF1F2")  # Latar Merah Danger
C_BLUE_BG      = colors.HexColor("#F0F9FF")  # Latar Info Biru


def create_custom_styles():
    base_styles = getSampleStyleSheet()
    styles = {}

    # Judul Cover
    styles['CoverMinistry'] = ParagraphStyle(
        'CoverMinistry',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=11,
        leading=14,
        textColor=C_NAVY_DARK,
        alignment=1, # Center
        spaceAfter=3,
    )
    styles['CoverSubMinistry'] = ParagraphStyle(
        'CoverSubMinistry',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=8.5,
        leading=11.5,
        textColor=C_TEXT_MUTED,
        alignment=1,
        spaceAfter=12,
    )
    styles['CoverBadge'] = ParagraphStyle(
        'CoverBadge',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=10,
        textColor=C_NAVY_DARK,
        alignment=1,
    )
    styles['CoverTitle'] = ParagraphStyle(
        'CoverTitle',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=19,
        leading=23,
        textColor=C_NAVY_DARK,
        alignment=1,
        spaceAfter=6,
    )
    styles['CoverRole'] = ParagraphStyle(
        'CoverRole',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=13,
        leading=17,
        textColor=C_GOLD_ACCENT,
        alignment=1,
        spaceAfter=12,
    )
    styles['CoverAppDesc'] = ParagraphStyle(
        'CoverAppDesc',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=9,
        leading=13.5,
        textColor=C_TEXT_MUTED,
        alignment=1,
        spaceAfter=20,
    )

    # Heading Bab & Sub-bab
    styles['ChapterHeading'] = ParagraphStyle(
        'ChapterHeading',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=12.5,
        leading=15.5,
        textColor=C_NAVY_DARK,
        spaceBefore=0,
        spaceAfter=3,
        keepWithNext=True,
    )
    styles['SubChapterHeading'] = ParagraphStyle(
        'SubChapterHeading',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=9.5,
        leading=12.5,
        textColor=C_NAVY_MID,
        spaceBefore=5,
        spaceAfter=3,
        keepWithNext=True,
    )
    styles['SubSubHeading'] = ParagraphStyle(
        'SubSubHeading',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8.5,
        leading=11.5,
        textColor=C_TEXT_MAIN,
        spaceBefore=4,
        spaceAfter=2,
        keepWithNext=True,
    )

    # Teks Paragraf Biasa
    styles['Body'] = ParagraphStyle(
        'Body',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=8,
        leading=11.2,
        textColor=C_TEXT_MAIN,
        spaceAfter=4,
    )
    styles['BodyJustify'] = ParagraphStyle(
        'BodyJustify',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=8,
        leading=11.2,
        textColor=C_TEXT_MAIN,
        alignment=4, # Justify
        spaceAfter=4,
    )
    styles['BulletText'] = ParagraphStyle(
        'BulletText',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.8,
        leading=10.5,
        textColor=C_TEXT_MAIN,
        spaceAfter=2,
    )
    styles['ImageCaption'] = ParagraphStyle(
        'ImageCaption',
        parent=base_styles['Normal'],
        fontName='Helvetica-Oblique',
        fontSize=7.2,
        leading=9.5,
        textColor=C_TEXT_MUTED,
        alignment=1, # Center
        spaceBefore=3,
        spaceAfter=2,
    )

    # Step-by-step
    styles['StepNumber'] = ParagraphStyle(
        'StepNumber',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=7.5,
        leading=9,
        textColor=colors.white,
        alignment=1,
    )
    styles['StepTitle'] = ParagraphStyle(
        'StepTitle',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=7.8,
        leading=10.5,
        textColor=C_NAVY_DARK,
    )
    styles['StepDesc'] = ParagraphStyle(
        'StepDesc',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.3,
        leading=9.8,
        textColor=C_TEXT_MUTED,
    )

    # Tabel Styles
    styles['TableHeader'] = ParagraphStyle(
        'TableHeader',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=7.4,
        leading=9.5,
        textColor=colors.white,
        alignment=1,
    )
    styles['TableCell'] = ParagraphStyle(
        'TableCell',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.2,
        leading=9.5,
        textColor=C_TEXT_MAIN,
    )
    styles['TableCellBold'] = ParagraphStyle(
        'TableCellBold',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=7.2,
        leading=9.5,
        textColor=C_NAVY_DARK,
    )
    styles['TableCellCenter'] = ParagraphStyle(
        'TableCellCenter',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.2,
        leading=9.5,
        textColor=C_TEXT_MAIN,
        alignment=1,
    )

    # Callout Box Text
    styles['CalloutTitle'] = ParagraphStyle(
        'CalloutTitle',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=7.8,
        leading=10,
        textColor=C_NAVY_DARK,
        spaceAfter=1,
    )
    styles['CalloutText'] = ParagraphStyle(
        'CalloutText',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.2,
        leading=9.8,
        textColor=C_TEXT_MAIN,
    )

    return styles


# ==============================================================================
# 3. HELPER FUNCTIONS UNTUK ELEMEN VISUAL DOKUMEN
# ==============================================================================
def make_callout(title, text, kind="info", styles=None, width=505):
    """
    Membuat kotak informasi bergaya profesional (Callout Box).
    kind: 'info', 'tip', 'warning', 'success', 'danger'
    """
    if kind == "info":
        bg_col = C_BLUE_BG
        border_col = C_NAVY_BLUE
        icon_label = "INFORMASI PENTING"
    elif kind == "tip":
        bg_col = C_GOLD_LIGHT
        border_col = C_GOLD_ACCENT
        icon_label = "TIPS OPERASIONAL PEGAWAI"
    elif kind == "warning":
        bg_col = C_AMBER_BG
        border_col = C_AMBER
        icon_label = "PERHATIAN KHUSUS"
    elif kind == "success":
        bg_col = C_EMERALD_BG
        border_col = C_EMERALD
        icon_label = "KEBIJAKAN RESMI"
    else:
        bg_col = C_ROSE_BG
        border_col = C_ROSE
        icon_label = "PERINGATAN RESMI"

    header_html = f"<b><font color='{border_col.hexval()}'>[ {icon_label} ]</font> &mdash; {title}</b>"
    
    cell_content = [
        Paragraph(header_html, styles['CalloutTitle']),
        Spacer(1, 1),
        Paragraph(text, styles['CalloutText'])
    ]

    t = Table([[cell_content]], colWidths=[width])
    t.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), bg_col),
        ('BOX', (0,0), (-1,-1), 0.75, C_BORDER_GRAY),
        ('LINELEFT', (0,0), (0,-1), 3.0, border_col),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 8),
        ('RIGHTPADDING', (0,0), (-1,-1), 8),
    ]))
    return t


def make_step_row(step_no, title, desc, styles, col_widths=[18, 290]):
    """
    Membuat baris panduan langkah bertahap (Step-by-step indicator).
    """
    badge = Table([[Paragraph(str(step_no), styles['StepNumber'])]], colWidths=[col_widths[0]], rowHeights=[14])
    badge.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), C_NAVY_BLUE),
        ('ALIGN', (0,0), (-1,-1), 'CENTER'),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 1),
        ('BOTTOMPADDING', (0,0), (-1,-1), 1),
        ('LEFTPADDING', (0,0), (-1,-1), 0),
        ('RIGHTPADDING', (0,0), (-1,-1), 0),
    ]))

    text_box = [
        Paragraph(f"<b>{title}</b>", styles['StepTitle']),
        Paragraph(desc, styles['StepDesc'])
    ]

    row_table = Table([[badge, text_box]], colWidths=[col_widths[0] + 4, col_widths[1]])
    row_table.setStyle(TableStyle([
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('LEFTPADDING', (0,0), (-1,-1), 0),
        ('RIGHTPADDING', (0,0), (-1,-1), 0),
        ('TOPPADDING', (0,0), (-1,-1), 1),
        ('BOTTOMPADDING', (0,0), (-1,-1), 2),
    ]))
    return row_table


def make_header_bar(title, styles):
    """
    Membuat pembatas bab dengan aksen warna Navy dan Gold.
    """
    title_p = Paragraph(f"<b>{title}</b>", styles['ChapterHeading'])
    line_gold = HRFlowable(width="28%", thickness=2.0, color=C_GOLD_ACCENT, spaceBefore=1, spaceAfter=4, hAlign='LEFT')
    return [title_p, line_gold]


def make_screenshot_box(img_path, width, height, caption_text, styles):
    """
    Membungkus screenshot dalam frame kartu elegan dengan caption terpusat.
    """
    img = Image(img_path, width=width, height=height)
    img.hAlign = 'CENTER'
    cap = Paragraph(f"<b>Gambar:</b> {caption_text}", styles['ImageCaption'])
    
    t = Table([[img], [cap]], colWidths=[width + 8])
    t.setStyle(TableStyle([
        ('ALIGN', (0,0), (-1,-1), 'CENTER'),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('BOX', (0,0), (-1,-1), 0.75, C_BORDER_GRAY),
        ('BACKGROUND', (0,0), (-1,-1), C_BG_LIGHT),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 4),
        ('RIGHTPADDING', (0,0), (-1,-1), 4),
    ]))
    t.hAlign = 'CENTER'
    return t


# ==============================================================================
# 4. KONTEN DOKUMEN BUKU PANDUAN PENGGUNA (ROLE PEGAWAI)
# ==============================================================================
def build_manual_book_story(styles, logo_path, img_dir):
    story = []

    # --------------------------------------------------------------------------
    # HALAMAN 1: COVER RESMI KEMENTERIAN PKP RI
    # --------------------------------------------------------------------------
    story.append(Spacer(1, 10))
    
    if os.path.exists(logo_path):
        img = Image(logo_path, width=130, height=73.5)
        img.hAlign = 'CENTER'
        story.append(img)
        story.append(Spacer(1, 10))

    story.append(Paragraph("KEMENTERIAN PERUMAHAN DAN KAWASAN PERMUKIMAN", styles['CoverMinistry']))
    story.append(Paragraph("REPUBLIK INDONESIA", styles['CoverMinistry']))
    story.append(Paragraph("Direktorat Jenderal Perumahan &bull; Subbagian Tata Usaha", styles['CoverSubMinistry']))

    story.append(HRFlowable(width="80%", thickness=1.5, color=C_NAVY_DARK, spaceBefore=2, spaceAfter=2))
    story.append(HRFlowable(width="35%", thickness=2.5, color=C_GOLD_ACCENT, spaceBefore=0, spaceAfter=18))

    badge_table = Table([[Paragraph("DOKUMEN RESMI PANDUAN OPERASIONAL SISTEM", styles['CoverBadge'])]], colWidths=[260], rowHeights=[16])
    badge_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), C_GOLD_LIGHT),
        ('BOX', (0,0), (-1,-1), 1, C_GOLD_ACCENT),
        ('ALIGN', (0,0), (-1,-1), 'CENTER'),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 2),
        ('BOTTOMPADDING', (0,0), (-1,-1), 2),
    ]))
    badge_table.hAlign = 'CENTER'
    story.append(badge_table)
    story.append(Spacer(1, 10))

    story.append(Paragraph("BUKU PANDUAN PENGGUNA<br/>(USER MANUAL BOOK)", styles['CoverTitle']))
    story.append(Paragraph("ROLE PEGAWAI (PEMOHON INDIVIDU)", styles['CoverRole']))

    story.append(Paragraph(
        "Panduan Praktis Tata Kelola Permohonan ATK Personal Staf Pegawai, "
        "Eksplorasi E-Katalog Persediaan, Usulan Pembelian Barang Baru, "
        "serta Pemantauan Alur Persetujuan Kasubag TU & Penerimaan Fisik Barang.",
        styles['CoverAppDesc']
    ))

    meta_data = [
        [Paragraph("<b>Nama Aplikasi</b>", styles['TableCellBold']), Paragraph(": ATEKA (Aplikasi Tata Kelola Persediaan & Pengadaan ATK)", styles['TableCell'])],
        [Paragraph("<b>Sasaran Pengguna</b>", styles['TableCellBold']), Paragraph(": Seluruh Staf, Pelaksana & Pejabat Fungsional Perorangan Kementerian PKP RI", styles['TableCell'])],
        [Paragraph("<b>Klasifikasi Dokumen</b>", styles['TableCellBold']), Paragraph(": Panduan Operasional Kedinasan (Internal Kementerian PKP RI)", styles['TableCell'])],
        [Paragraph("<b>Versi Rilis Sistem</b>", styles['TableCellBold']), Paragraph(": Versi 2.0 (Edisi Khusus Pemohon Individu Pegawai - 2026)", styles['TableCell'])],
        [Paragraph("<b>Penerbit & Pengelola</b>", styles['TableCellBold']), Paragraph(": Subbagian Tata Usaha, Kementerian PKP RI", styles['TableCell'])],
    ]
    meta_table = Table(meta_data, colWidths=[120, 360])
    meta_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), C_BG_LIGHT),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 4),
        ('BOTTOMPADDING', (0,0), (-1,-1), 4),
        ('LEFTPADDING', (0,0), (-1,-1), 8),
        ('RIGHTPADDING', (0,0), (-1,-1), 8),
    ]))
    meta_table.hAlign = 'CENTER'
    story.append(meta_table)

    story.append(Spacer(1, 24))
    story.append(Paragraph("<font size=7.5 color='#94A3B8'>JAKARTA &bull; TAHUN ANGGARAN 2026</font>", styles['CoverSubMinistry']))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 2: RINGKASAN EKSEKUTIF, MATRIKS PERAN & DAFTAR ISI
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("KATA PENGANTAR & PROFIL PERAN PEGAWAI", styles))
    
    story.append(Paragraph(
        "<b>Sistem Inventori ATEKA</b> merupakan platform digital terpadu Kementerian Perumahan dan Kawasan Permukiman RI "
        "yang memberikan kemudahan bagi setiap aparatur sipil negara dan staf kerja untuk mengelola kebutuhan alat tulis kantor secara mandiri. "
        "<b>Role Pegawai (Pemohon Individu)</b> dikhususkan untuk memfasilitasi kebutuhan perlengkapan kerja personal harian "
        "maupun bulanan secara tertib administrasi, cepat, dan transparan tanpa perlu formulir kertas fisik konvensional.",
        styles['BodyJustify']
    ))

    story.append(make_callout(
        "Karakteristik & Wewenang Peran Pegawai (Pemohon Individu)",
        "Akun Pegawai diperuntukkan bagi setiap staf pelaksana di lingkungan Kementerian PKP RI dengan karakteristik:<br/>"
        "&bull; <b>Peruntukan Personal :</b> Ditujukan untuk mendukung pekerjaan harian meja kerja masing-masing staf.<br/>"
        "&bull; <b>Akses Mandiri :</b> Setiap pegawai memiliki akun tersendiri yang terikat dengan Nomor Induk Pegawai (NIP).<br/>"
        "&bull; <b>Fleksibilitas Pengajuan :</b> Dapat meminta stok persediaan gudang harian maupun mengusulkan barang baru (Beli Baru).<br/>"
        "&bull; <b>Pelacakan Transparan :</b> Memantau status tiket secara langsung dari meja verifikasi Kasubag TU hingga penyerahan fisik.",
        kind="info",
        styles=styles,
        width=505
    ))

    story.append(Spacer(1, 4))
    story.append(Paragraph("Matriks Perbandingan Wewenang: Pegawai (Individu) vs Subdirektorat (Kolektif)", styles['SubChapterHeading']))
    comp_data = [
        [Paragraph("<b>Parameter</b>", styles['TableHeader']), Paragraph("<b>Pegawai (Pemohon Individu)</b>", styles['TableHeader']), Paragraph("<b>Subdirektorat (Pemohon Kolektif)</b>", styles['TableHeader'])],
        [Paragraph("<b>Tujuan Permohonan</b>", styles['TableCellBold']), Paragraph("Kebutuhan harian personal staf perorangan", styles['TableCell']), Paragraph("Kebutuhan operasional bersama satu unit kerja/seksi", styles['TableCell'])],
        [Paragraph("<b>Volume Kuantitas</b>", styles['TableCellBold']), Paragraph("Jumlah terbatas/satuan kecil (misal: 1 rim kertas, 2 pulpen)", styles['TableCell']), Paragraph("Jumlah sedang hingga besar (misal: 10 rim kertas, 5 dos map)", styles['TableCell'])],
        [Paragraph("<b>Pilihan Saat Registrasi</b>", styles['TableCellBold']), Paragraph("Memilih radio 'Pegawai', tidak memerlukan pemilihan divisi", styles['TableCell']), Paragraph("Memilih radio 'Subdirektorat' dan memilih 1 dari 4 unit kerja", styles['TableCell'])],
        [Paragraph("<b>Jalur Usulan Beli Baru</b>", styles['TableCellBold']), Paragraph("Dapat mengusulkan pengadaan ATK khusus meja kerja", styles['TableCell']), Paragraph("Dapat mengusulkan pengadaan kebutuhan besar divisi", styles['TableCell'])],
        [Paragraph("<b>Penerimaan Fisik</b>", styles['TableCellBold']), Paragraph("Diterima langsung oleh pegawai bersangkutan di unitnya", styles['TableCell']), Paragraph("Diterima oleh operator/penanggung jawab subdirektorat", styles['TableCell'])],
    ]
    comp_table = Table(comp_data, colWidths=[95, 205, 205])
    comp_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    for r in range(1, len(comp_data)):
        if r % 2 == 0:
            comp_table.setStyle(TableStyle([('BACKGROUND', (0, r), (-1, r), C_BG_LIGHT)]))
    story.append(comp_table)

    story.append(Spacer(1, 6))
    story.extend(make_header_bar("DAFTAR ISI PANDUAN PENGGUNA", styles))

    toc_data = [
        [Paragraph("<b>Bab</b>", styles['TableHeader']), Paragraph("<b>Pokok Pembahasan & Modul Sistem ATEKA</b>", styles['TableHeader']), Paragraph("<b>Tangkapan Layar</b>", styles['TableHeader']), Paragraph("<b>Halaman</b>", styles['TableHeader'])],
        [Paragraph("<b>Bab I</b>", styles['TableCellBold']), Paragraph("<b>Registrasi Akun Pegawai (Pemohon Individu)</b>", styles['TableCell']), Paragraph("image2.png (Pendaftaran Akun Pegawai)", styles['TableCellCenter']), Paragraph("Hal 3", styles['TableCellCenter'])],
        [Paragraph("<b>Bab II</b>", styles['TableCellBold']), Paragraph("<b>Login Sistem & Pemulihan Kata Sandi Berbasis NIP</b>", styles['TableCell']), Paragraph("image1.png & image3.png", styles['TableCellCenter']), Paragraph("Hal 4", styles['TableCellCenter'])],
        [Paragraph("<b>Bab III</b>", styles['TableCellBold']), Paragraph("<b>Eksplorasi Dashboard Pegawai & 4 Indikator Status</b>", styles['TableCell']), Paragraph("image4.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 5", styles['TableCellCenter'])],
        [Paragraph("<b>Bab IV</b>", styles['TableCellBold']), Paragraph("<b>E-Katalog ATK & Pengecekan Ketersediaan Stok Gudang</b>", styles['TableCell']), Paragraph("image8.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 6", styles['TableCellCenter'])],
        [Paragraph("<b>Bab V</b>", styles['TableCellBold']), Paragraph("<b>Formulir Permintaan ATK dari Persediaan Gudang</b>", styles['TableCell']), Paragraph("image6.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 7", styles['TableCellCenter'])],
        [Paragraph("<b>Bab VI</b>", styles['TableCellBold']), Paragraph("<b>Usulan Pengadaan Barang ATK Baru (Belanja Supplier)</b>", styles['TableCell']), Paragraph("image7.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 8", styles['TableCellCenter'])],
        [Paragraph("<b>Bab VII</b>", styles['TableCellBold']), Paragraph("<b>Pemantauan Riwayat Permintaan & Verifikasi Kasubag TU</b>", styles['TableCell']), Paragraph("image5.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 9", styles['TableCellCenter'])],
        [Paragraph("<b>Bab VIII</b>", styles['TableCellBold']), Paragraph("<b>Pusat Notifikasi & Prosedur Serah Terima Fisik Barang</b>", styles['TableCell']), Paragraph("image9.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 10", styles['TableCellCenter'])],
        [Paragraph("<b>Bab IX</b>", styles['TableCellBold']), Paragraph("<b>Panduan Pemecahan Masalah (FAQ) & Meja Bantuan TU</b>", styles['TableCell']), Paragraph("FAQ & Alur Bantuan", styles['TableCellCenter']), Paragraph("Hal 11", styles['TableCellCenter'])],
    ]
    toc_table = Table(toc_data, colWidths=[40, 265, 150, 50])
    toc_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_DARK),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 2.8),
        ('BOTTOMPADDING', (0,0), (-1,-1), 2.8),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    for r in range(1, len(toc_data)):
        if r % 2 == 0:
            toc_table.setStyle(TableStyle([('BACKGROUND', (0, r), (-1, r), C_BG_LIGHT)]))
    story.append(toc_table)

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 3: BAB I - REGISTRASI AKUN PEGAWAI (image2.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB I: REGISTRASI AKUN PEGAWAI (PEMOHON INDIVIDU)", styles))
    
    story.append(Paragraph(
        "Setiap pegawai atau staf kerja di lingkungan Kementerian PKP RI dapat melakukan pendaftaran akun secara mandiri "
        "melalui portal ATEKA. Form registrasi dirancang ringkas dengan validasi NIP otomatis untuk memastikan keamanan data.",
        styles['BodyJustify']
    ))

    img2_path = os.path.join(img_dir, "image2.png")
    # Asli: 450 x 916 (rasio 0.491) -> lebar 170, tinggi 346
    img2_box = make_screenshot_box(img2_path, 170, 346, "Form Registrasi Akun Pegawai (Pemohon Individu)", styles)

    reg_steps = [
        make_step_row(1, "Akses Halaman Pendaftaran", "Buka peramban (browser) di ponsel atau laptop, akses <b>/register</b> atau klik tautan <i>'Daftar Akun Baru'</i> pada laman Login.", styles),
        make_step_row(2, "Isi Nama Lengkap & Gelar", "Ketik nama lengkap Anda (contoh: <i>Bagus Sadewo</i>). Kolom <b>Gelar Akademik (Opsional)</b> dapat diisi gelar seperti <i>S.Kom., S.T., M.Si.</i> untuk kelengkapan administrasi tanda terima resmi.", styles),
        make_step_row(3, "Isi Alamat Email", "Gunakan email dinas (misal: <i>nama@perumahan.go.id</i>) atau email aktif yang digunakan untuk korespondensi kerja.", styles),
        make_step_row(4, "Masukkan NIP 18 Digit", "Input Nomor Induk Pegawai resmi Anda. NIP berfungsi sebagai identitas unik dinas sekaligus kunci utama fitur <b>Reset Lupa Password Mandiri</b>.", styles),
        make_step_row(5, "Pilih Jenis: 'Pegawai'", "Klik kartu radio button <b>'Pegawai'</b> dengan badge hitam <b>'Pemohon Individu'</b> (tampak pada screenshot di sebelah kiri). Pilihan ini khusus untuk permohonan staf perorangan.", styles),
        make_step_row(6, "Bebas Dropdown Divisi", "Berbeda dengan role Subdirektorat, saat memilih <b>Pegawai</b>, Anda tidak perlu memilih divisi karena otomatis terdaftar sebagai pemohon individu pada unit kerja Anda.", styles),
        make_step_row(7, "Buat Kata Sandi & Konfirmasi", "Ketikkan password akun minimal 6 karakter, lalu masukkan ulang pada kolom konfirmasi kata sandi.", styles),
        make_step_row(8, "Klik 'Daftarkan Akun'", "Sistem langsung memvalidasi data dan melakukan <b>Login Otomatis</b> ke Dashboard ATEKA tanpa perlu verifikasi email manual.", styles),
    ]

    col_right_content = reg_steps
    reg_table = Table([[img2_box, col_right_content]], colWidths=[185, 320])
    reg_table.setStyle(TableStyle([
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('LEFTPADDING', (0,0), (-1,-1), 0),
        ('RIGHTPADDING', (0,0), (-1,-1), 0),
        ('TOPPADDING', (0,0), (-1,-1), 0),
        ('BOTTOMPADDING', (0,0), (-1,-1), 0),
    ]))
    story.append(reg_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Kewajiban Penggunaan NIP Resmi Pegawai",
        "Nomor Induk Pegawai (NIP) wajib valid dan benar. Sistem mengenali format NIP baik dengan spasi maupun tanpa spasi. "
        "NIP ini merupakan identitas kedinasan yang digunakan untuk seluruh riwayat serah terima fisik barang.",
        kind="warning",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 4: BAB II - LOGIN & LUPA KATA SANDI MANDIRI (image1.png & image3.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB II: LOGIN SISTEM & PEMULIHAN KATA SANDI BERBASIS NIP", styles))
    
    story.append(Paragraph(
        "Akses masuk ke sistem ATEKA dapat dilakukan dengan cepat menggunakan email dan kata sandi yang telah didaftarkan. "
        "Bila Anda lupa kata sandi, sistem menyediakan fitur reset mandiri seketika hanya dengan memasukkan NIP terdaftar.",
        styles['BodyJustify']
    ))

    img1_path = os.path.join(img_dir, "image1.png")
    # Asli 379 x 770 -> lebar 130, tinggi 264
    img1_box = make_screenshot_box(img1_path, 130, 264, "Form Login Masuk Sistem", styles)

    img3_path = os.path.join(img_dir, "image3.png")
    # Asli 477 x 969 -> lebar 130, tinggi 264
    img3_box = make_screenshot_box(img3_path, 130, 264, "Form Reset Kata Sandi NIP", styles)

    # Panel Kiri: Login
    login_panel = [
        Paragraph("<b>2.1 Masuk ke Akun Anda (/login)</b>", styles['SubChapterHeading']),
        img1_box,
        Spacer(1, 3),
        Paragraph("<b>Langkah Masuk Sistem:</b>", styles['StepTitle']),
        Paragraph("&bull; Masukkan <b>Alamat Email</b> yang telah terdaftar.<br/>"
                  "&bull; Masukkan <b>Kata Sandi (Password)</b> Anda.<br/>"
                  "&bull; Klik tombol biru <b>'Masuk ke Sistem'</b>.<br/>"
                  "&bull; Klik ikon mata jika ingin melihat karakter sandi.<br/>"
                  "&bull; Tersedia tombol <i>Akses Cepat Demo</i> untuk simulasi pelatihan sistem inventori kantor.", styles['BulletText']),
    ]

    # Panel Kanan: Lupa Password
    lupa_panel = [
        Paragraph("<b>2.2 Reset Sandi Mandiri (/lupa-password)</b>", styles['SubChapterHeading']),
        img3_box,
        Spacer(1, 3),
        Paragraph("<b>Langkah Reset Kata Sandi:</b>", styles['StepTitle']),
        Paragraph("&bull; Klik link <b>'Lupa Password?'</b> di form login.<br/>"
                  "&bull; Masukkan <b>NIP 18 Digit</b> resmi Anda.<br/>"
                  "&bull; Box hijau mengonfirmasi: <i>Verifikasi Keamanan Otomatis</i> langsung tanpa kode email.<br/>"
                  "&bull; Masukkan <b>Password Baru</b> (min 6 karakter).<br/>"
                  "&bull; Ulangi pada <b>Konfirmasi Password Baru</b>.<br/>"
                  "&bull; Klik <b>'Simpan & Perbarui Password'</b>. Anda langsung dapat login kembali seketika!", styles['BulletText']),
    ]

    auth_table = Table([[login_panel, lupa_panel]], colWidths=[248, 257])
    auth_table.setStyle(TableStyle([
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('LEFTPADDING', (0,0), (-1,-1), 2),
        ('RIGHTPADDING', (0,0), (-1,-1), 2),
        ('TOPPADDING', (0,0), (-1,-1), 0),
        ('BOTTOMPADDING', (0,0), (-1,-1), 0),
    ]))
    story.append(auth_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Kelebihan Fitur Reset Password Berbasis NIP Tanpa Ketergantungan Email",
        "Sistem ini dirancang khusus agar staf tidak terhambat saat jaringan internet kantor membatasi pengiriman email keluar (SMTP). "
        "Validasi NIP menjamin akun pegawai tetap aman dan dapat dipulihkan kapan pun secara mandiri.",
        kind="tip",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 5: BAB III - EKSPLORASI DASHBOARD PEGAWAI (image4.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB III: EKSPLORASI DASHBOARD PEGAWAI & 4 INDIKATOR STATUS", styles))
    
    story.append(Paragraph(
        "Dashboard Pegawai menyajikan ringkasan visual status permohonan ATK personal Anda. "
        "Melalui halaman ini, pegawai dapat memantau pergerakan berkas, mengakses e-katalog, dan membuat pengajuan baru secara cepat.",
        styles['BodyJustify']
    ))

    img4_path = os.path.join(img_dir, "image4.png")
    # Asli 2282 x 1310 -> lebar 435, tinggi 250
    img4_box = make_screenshot_box(img4_path, 435, 250, "Dashboard Pegawai - Bagus Sadewo (IT | Pemohon Individu)", styles)
    story.append(img4_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Komponen Antarmuka Utama & Penjelasan 4 Indikator KPI", styles['SubChapterHeading']))

    dash_guide_data = [
        [Paragraph("<b>Elemen Dashboard</b>", styles['TableHeader']), Paragraph("<b>Tampilan pada Screenshot (Gambar di Atas) & Fungsinya</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Header & Identitas</b>", styles['TableCellBold']), Paragraph("Menampilkan ucapan: <i>Selamat Datang, Bagus Sadewo</i>, unit divisi <i>IT</i>, serta badge biru <i>'Pegawai (Pemohon Individu)'</i>.", styles['TableCell'])],
        [Paragraph("<b>2. Tombol Aksi Cepat</b>", styles['TableCellBold']), Paragraph("Tiga tombol utama: <b>'+ Buat Pengajuan ATK Baru'</b> (ke form stok gudang), <b>'Katalog & Ketersediaan Stok'</b>, dan <b>'Riwayat Pengajuan Saya'</b>.", styles['TableCell'])],
        [Paragraph("<b>3. Filter Periode Data</b>", styles['TableCellBold']), Paragraph("Bilah pemilih Bulan (Oktober) dan Tahun (2026) untuk memantau frekuensi permohonan ATK personal dalam periode tertentu.", styles['TableCell'])],
        [Paragraph("<b>4. Empat Kartu KPI</b>", styles['TableCellBold']), Paragraph("<b>Menunggu Review (0):</b> Pengajuan dalam antrean meja Kasubag TU.<br/>"
                                                                                 "<b>Pengadaan Siap (2):</b> Barang pesanan baru telah dibeli dan tersedia di gudang.<br/>"
                                                                                 "<b>Selesai Diterima (1):</b> Fisik ATK telah diterima staf dan didokumentasikan.<br/>"
                                                                                 "<b>Ditolak (1):</b> Berkas tidak disetujui TU disertai alasan tertulis resmi.", styles['TableCell'])],
        [Paragraph("<b>5. Navigasi Sidebar</b>", styles['TableCellBold']), Paragraph("Menu samping kiri untuk beralih instan antara Dashboard, Riwayat Pengajuan, Form Stok, Form Beli Baru, dan Katalog ATK.", styles['TableCell'])],
    ]
    dash_guide_table = Table(dash_guide_data, colWidths=[110, 395])
    dash_guide_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(dash_guide_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Indikator 'Pengadaan Siap' Khusus Pegawai",
        "Ketika kartu 'Pengadaan Siap' bernilai lebih dari 0, ini menandakan usulan belanja barang yang sebelumnya Anda minta "
        "telah berhasil dibeli oleh Subbag TU dan siap diserahterimakan ke meja kerja Anda oleh petugas PIC Distribusi.",
        kind="info",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 6: BAB IV - E-KATALOG ATK & CEK STOK GUDANG (image8.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB IV: E-KATALOG ATK & PENGECEKAN KETERSEDIAAN STOK GUDANG", styles))
    
    story.append(Paragraph(
        "Modul E-Katalog merupakan etalase digital transparan yang menyajikan seluruh daftar alat tulis kantor yang tersedia "
        "secara fisik di ruang penyimpanan gudang Subbagian Tata Usaha Kementerian PKP RI.",
        styles['BodyJustify']
    ))

    img8_path = os.path.join(img_dir, "image8.png")
    # Asli 2282 x 1310 -> lebar 435, tinggi 250
    img8_box = make_screenshot_box(img8_path, 435, 250, "Katalog Alat Tulis Kantor & Ketersediaan Stok Fisik Gudang", styles)
    story.append(img8_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Eksplorasi Fitur Katalog & Cara Mengajukan Barang Langsung", styles['SubChapterHeading']))

    katalog_features = [
        [Paragraph("<b>Fitur & Elemen</b>", styles['TableHeader']), Paragraph("<b>Penjelasan Fungsional & Panduan Penggunaan Pegawai</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Bilah Pencarian</b>", styles['TableCellBold']), Paragraph("Ketik nama atau kode barang pada kolom pencarian (contoh: <i>Map Coklat, Kertas A4, Pulpen, Spidol</i>) untuk menemukan item seketika.", styles['TableCell'])],
        [Paragraph("<b>2. Filter Kategori</b>", styles['TableCellBold']), Paragraph("Pilih kategori ATK (Kertas & Buku, Pengarsipan, Alat Tulis, Perekat, Aksesoris Meja) guna menyaring daftar barang.", styles['TableCell'])],
        [Paragraph("<b>3. Kartu Barang Interaktif</b>", styles['TableCellBold']), Paragraph("Menampilkan visual barang, kode resmi (misal: <i>ATK-004</i>), nama barang (<i>Map Coklat</i>), dan sisa fisik (<i>Sisa Stok Fisik: 5 Pcs</i>).", styles['TableCell'])],
        [Paragraph("<b>4. Indikator Meter Stok</b>", styles['TableCellBold']), Paragraph("<b>Hijau (Tersedia):</b> Stok aman untuk diajukan staf.<br/>"
                                                                                   "<b>Kuning (Menipis):</b> Sisa 1 s.d. 10 unit, segera ajukan sebelum habis.<br/>"
                                                                                   "<b>Merah (Habis):</b> Stok gudang 0 unit.", styles['TableCell'])],
        [Paragraph("<b>5. Tombol '+ Ajukan'</b>", styles['TableCellBold']), Paragraph("Klik tombol <b>'+ Ajukan Barang Ini'</b> pada kartu barang. Anda langsung diarahkan ke form pengajuan dengan barang tersebut sudah terisi otomatis!", styles['TableCell'])],
        [Paragraph("<b>6. Restock Barang Habis</b>", styles['TableCellBold']), Paragraph("Bila barang berstatus habis (0 unit), muncul tombol khusus <b>'Pesan Barang Ini (Restock)'</b> untuk langsung membuat tiket belanja pengadaan baru.", styles['TableCell'])],
    ]
    katalog_table = Table(katalog_features, colWidths=[115, 390])
    katalog_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(katalog_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Manfaat Mengecek Katalog Sebelum Mengajukan",
        "Dengan mengecek katalog, pegawai dapat memastikan bahwa barang yang diajukan benar-benar siap diambil dari gudang, "
        "sehingga proses persetujuan dan penyerahan barang dapat berlangsung pada hari yang sama.",
        kind="tip",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 7: BAB V - FORMULIR PERMINTAAN STOK GUDANG (image6.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB V: FORMULIR PERMINTAAN ATK DARI PERSEDIAAN GUDANG", styles))
    
    story.append(Paragraph(
        "Formulir ini digunakan saat pegawai memerlukan alat tulis kantor yang <b>stok fisiknya sudah tersedia di gudang</b>. "
        "Sistem mendukung pengajuan beberapa item sekaligus untuk mencukupi perlengkapan meja kerja Anda.",
        styles['BodyJustify']
    ))

    img6_path = os.path.join(img_dir, "image6.png")
    # Asli 2282 x 1310 -> lebar 435, tinggi 250
    img6_box = make_screenshot_box(img6_path, 435, 250, "Formulir Pengajuan Permintaan ATK - Stok Gudang", styles)
    story.append(img6_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Panduan Langkah Pengisian Formulir Permintaan Persediaan", styles['SubChapterHeading']))

    pengajuan_steps_data = [
        [Paragraph("<b>Tahapan Pengisian</b>", styles['TableHeader']), Paragraph("<b>Rincian Tindakan pada Antarmuka (Gambar di Atas)</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Jenis Permintaan</b>", styles['TableCellBold']), Paragraph("Dropdown terpilih otomatis <b>'Individu (Staf / Pegawai)'</b> untuk permohonan staf perorangan.", styles['TableCell'])],
        [Paragraph("<b>2. Pemohon / Unit Kerja</b>", styles['TableCellBold']), Paragraph("Terisi otomatis oleh sistem dengan nama dan seksi Anda: <b>Bagus Sadewo (IT)</b>.", styles['TableCell'])],
        [Paragraph("<b>3. Keperluan / Catatan</b>", styles['TableCellBold']), Paragraph("Kolom catatan opsional (contoh: <i>'Kebutuhan operasional rapat koordinasi dan administrasi berkas bulan ini'</i>).", styles['TableCell'])],
        [Paragraph("<b>4. Tombol 'Lihat Katalog'</b>", styles['TableCellBold']), Paragraph("Klik tombol <b>'Lihat Katalog & Stok'</b> jika ingin memeriksa kembali sisa stok fisik gudang sebelum memilih barang.", styles['TableCell'])],
        [Paragraph("<b>5. Pilih Barang & Kuantitas</b>", styles['TableCellBold']), Paragraph("Pilih barang dari dropdown dan tentukan jumlah unit yang dibutuhkan sesuai batas wajar penggunaan personal.", styles['TableCell'])],
        [Paragraph("<b>6. Multi-Item (+ Tambah)</b>", styles['TableCellBold']), Paragraph("Gunakan tombol <b>'+ Tambah Barang Lain'</b> untuk meminta barang kedua (misal: butuh pulpen dan map sekaligus).", styles['TableCell'])],
        [Paragraph("<b>7. Kirim Pengajuan</b>", styles['TableCellBold']), Paragraph("Klik tombol <b>'Kirim Pengajuan ke Subbag TU'</b>. Sistem langsung menerbitkan kode tiket resmi <b>REQ-YYYYMMDD-XXXX</b>.", styles['TableCell'])],
    ]
    pengajuan_table = Table(pengajuan_steps_data, colWidths=[115, 390])
    pengajuan_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(pengajuan_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Konversi Otomatis ke Alur Beli Baru (REQ-BUY) Bila Stok Kurang",
        "Apabila barang yang Anda minta ternyata stok fisiknya habis di gudang atau kuantitas melebihi persediaan, "
        "sistem ATEKA <b>secara otomatis</b> mengalihkan tiket menjadi pengadaan beli baru (<b>REQ-BUY-...</b>) tanpa perlu mengisi ulang!",
        kind="success",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 8: BAB VI - PENGADAAN BARANG ATK BARU / BELI BARU (image7.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB VI: USULAN PENGADAAN BARANG ATK BARU (BELI SUPPLIER)", styles))
    
    story.append(Paragraph(
        "Formulir ini digunakan saat pegawai membutuhkan alat tulis atau perlengkapan kantor yang <b>belum tersedia dalam "
        "master persediaan gudang</b>, sehingga memerlukan pembelanjaan baru ke rekanan/supplier resmi oleh Subbag TU.",
        styles['BodyJustify']
    ))

    img7_path = os.path.join(img_dir, "image7.png")
    # Asli 2282 x 1310 -> lebar 435, tinggi 250
    img7_box = make_screenshot_box(img7_path, 435, 250, "Formulir Permintaan Pengadaan Barang ATK Baru (Beli Baru)", styles)
    story.append(img7_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Panduan Pengisian Formulir Pengadaan & Pemilihan Satuan Fleksibel", styles['SubChapterHeading']))

    pengadaan_guide_data = [
        [Paragraph("<b>Field Formulir</b>", styles['TableHeader']), Paragraph("<b>Ketentuan Pengisian & Panduan (Sesuai Gambar Screenshot)</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Box Alur Pembelian</b>", styles['TableCellBold']), Paragraph("Petunjuk alur mengingatkan bahwa barang akan dibelanjakan ke supplier dan diserahkan ke meja kerja Anda setelah tiba.", styles['TableCell'])],
        [Paragraph("<b>2. Jenis & Pemohon</b>", styles['TableCellBold']), Paragraph("Terisi otomatis: <b>Individu (Staf / Pegawai)</b> & Pemohon: <b>Bagus Sadewo (IT)</b>.", styles['TableCell'])],
        [Paragraph("<b>3. Alasan Pengadaan</b>", styles['TableCellBold']), Paragraph("Tuliskan justifikasi urgensi kerja (contoh: <i>'Kebutuhan map dokumen akreditasi, stok belum tersedia di gudang...'</i>).", styles['TableCell'])],
        [Paragraph("<b>4. Nama Barang yang Diminta</b>", styles['TableCellBold']), Paragraph("Ketik nama barang (contoh pada screenshot: <i>Map Coklat</i>). Dilengkapi saran autocomplete jika nama barang terdeteksi.", styles['TableCell'])],
        [Paragraph("<b>5. Jumlah & Satuan Fleksibel</b>", styles['TableCellBold']), Paragraph("Ketik jumlah (misal: <i>1</i>). Pilih satuan dari dropdown: <b>Pcs, Rim, Dos/Dus, Pak, Box, Lusin, Buah, Lembar, Buku, Set, Roll, dll.</b>", styles['TableCell'])],
        [Paragraph("<b>6. Spesifikasi / Merk</b>", styles['TableCellBold']), Paragraph("Tuliskan spesifikasi detail (warna, ukuran F4/A4, merk rekomendasi) pada kolom opsional agar pembelian sesuai harapan.", styles['TableCell'])],
        [Paragraph("<b>7. Kirim Usulan Pengadaan</b>", styles['TableCellBold']), Paragraph("Klik tombol <b>'Kirim Usulan Pengadaan ke Subbag TU'</b>. Berkas diterbitkan dengan kode tiket <b>REQ-BUY-YYYYMMDD-XXXX</b>.", styles['TableCell'])],
    ]
    pengadaan_table = Table(pengadaan_guide_data, colWidths=[120, 385])
    pengadaan_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(pengadaan_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Ketentuan Pengusulan Barang Beli Baru untuk Pegawai",
        "Pengadaan barang ke rekanan memerlukan proses verifikasi anggaran dan waktu pengiriman. "
        "Ajukan usulan minimal beberapa hari sebelum barang tersebut benar-benar diperlukan untuk mendukung agenda kerja dinas.",
        kind="warning",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 9: BAB VII - RIWAYAT PERMINTAAN & VERIFIKASI TU (image5.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB VII: PEMANTAUAN RIWAYAT PERMINTAAN & VERIFIKASI TU", styles))
    
    story.append(Paragraph(
        "Menu Riwayat Pengajuan menyajikan pelacakan transparan seluruh berkas permohonan yang diajukan oleh akun Anda. "
        "Pegawai dapat memantau kuantitas hasil verifikasi Kasubag TU serta melihat progres penerimaan barang.",
        styles['BodyJustify']
    ))

    img5_path = os.path.join(img_dir, "image5.png")
    # Asli 2282 x 1310 -> lebar 435, tinggi 250
    img5_box = make_screenshot_box(img5_path, 435, 250, "Tabel Riwayat Permintaan Anda & Status Persetujuan TU", styles)
    story.append(img5_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Membaca Komponen Kolom Tabel & Status Verifikasi Kasubag TU", styles['SubChapterHeading']))

    riwayat_guide_data = [
        [Paragraph("<b>Kolom Tabel</b>", styles['TableHeader']), Paragraph("<b>Penjelasan Informasi yang Ditampilkan (Sesuai Gambar Screenshot)</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Kode Pengajuan</b>", styles['TableCellBold']), Paragraph("Menampilkan kode unik tiket (contoh: <b>REQ-BUY-20261012-H1SC</b>) dan badge jenis tiket <i>'Pengadaan (Beli TU)'</i>.", styles['TableCell'])],
        [Paragraph("<b>2. Tipe & Tanggal</b>", styles['TableCellBold']), Paragraph("Menampilkan badge <i>Individu</i> beserta tanggal dan jam permohonan dikirimkan (misal: <i>12/10/2026 12:59</i>).", styles['TableCell'])],
        [Paragraph("<b>3. Barang yang Diminta</b>", styles['TableCellBold']), Paragraph("Menampilkan nama barang dan status verifikasi kuantitas: badge hijau <b>Disetujui: 1 Pcs</b> dan spesifikasi yang diajukan.", styles['TableCell'])],
        [Paragraph("<b>4. Tombol Aksi Cepat</b>", styles['TableCellBold']), Paragraph("Tersedia tombol hijau <b>'+ Permintaan Pengadaan (Beli Baru)'</b> dan biru <b>'+ Buat Pengajuan (Stok)'</b> di atas tabel.", styles['TableCell'])],
        [Paragraph("<b>5. Empat Kartu Status</b>", styles['TableCellBold']), Paragraph("Menampilkan ringkasan status berkas: <i>Menunggu Review (0)</i>, <i>Disetujui TU (3)</i>, <i>Selesai Diterima (1)</i>, dan <i>Ditolak (1)</i>.", styles['TableCell'])],
        [Paragraph("<b>6. Tombol Bukti Serah Terima</b>", styles['TableCellBold']), Paragraph("Saat status berubah menjadi <b>Selesai Diterima</b>, tombol <b>'Bukti'</b> akan aktif untuk mengunduh lembar tanda terima resmi.", styles['TableCell'])],
    ]
    riwayat_table = Table(riwayat_guide_data, colWidths=[120, 385])
    riwayat_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(riwayat_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Rasionalisasi Kuota Persetujuan Kasubag TU",
        "Kasubag TU dapat melakukan penyesuaian jumlah unit barang yang disetujui guna menjaga ketersediaan stok gudang dan asas "
        "pemerataan bagi seluruh pegawai. Kuantitas akhir yang akan Anda terima selalu tercantum pada badge hijau 'Disetujui'.",
        kind="info",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 10: BAB VIII - PUSAT NOTIFIKASI & SERAH TERIMA FISIK (image9.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB VIII: PUSAT NOTIFIKASI & PROSEDUR SERAH TERIMA FISIK BARANG", styles))
    
    story.append(Paragraph(
        "Sistem ATEKA secara otomatis mengirimkan notifikasi saat barang yang diajukan telah disetujui, tiba di gudang, atau siap diserahkan. "
        "Bab ini memandu pegawai memahami alur notifikasi hingga proses penerimaan fisik barang di meja kerja.",
        styles['BodyJustify']
    ))

    img9_path = os.path.join(img_dir, "image9.png")
    # Asli 2282 x 1310 -> lebar 435, tinggi 250
    img9_box = make_screenshot_box(img9_path, 435, 250, "Pusat Notifikasi Navbar - Pemberitahuan Ketersediaan Barang", styles)
    story.append(img9_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Mekanisme Notifikasi & Prosedur Penerimaan Fisik Barang di Meja Kerja", styles['SubChapterHeading']))

    dist_guide_data = [
        [Paragraph("<b>Tahapan Prosedur</b>", styles['TableHeader']), Paragraph("<b>Penjelasan Teknis & Aksi Pegawai (Sesuai Gambar Screenshot)</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Lonceng Notifikasi</b>", styles['TableCellBold']), Paragraph("Ikon lonceng pada navbar atas menampilkan badge angka merah ketika ada pemberitahuan baru (contoh: <i>2 notifikasi belum dibaca</i>).", styles['TableCell'])],
        [Paragraph("<b>2. Pesan 'Barang Tersedia'</b>", styles['TableCellBold']), Paragraph("Pop-up menampilkan notif: <i>'Barang Telah Tersedia (Baru) - Barang Map Hijau telah dibeli Subbag TU dan kini tersedia di persediaan ATK'</i>.", styles['TableCell'])],
        [Paragraph("<b>3. Tombol 'Tandai Dibaca'</b>", styles['TableCellBold']), Paragraph("Klik tombol biru <i>'Tandai Dibaca'</i> untuk membersihkan notifikasi yang sudah Anda ketahui.", styles['TableCell'])],
        [Paragraph("<b>4. Penyerahan oleh PIC</b>", styles['TableCellBold']), Paragraph("Petugas gudang (PIC Distribusi) mendatangi meja kerja pegawai pemohon untuk menyerahkan fisik barang sesuai tiket.", styles['TableCell'])],
        [Paragraph("<b>5. Pengecekan Bersama</b>", styles['TableCellBold']), Paragraph("Pegawai memeriksa fisik barang bersama PIC Distribusi (kelayakan fisik, warna, kelengkapan kuantitas).", styles['TableCell'])],
        [Paragraph("<b>6. Bukti Foto & Tanda Terima</b>", styles['TableCellBold']), Paragraph("PIC mengambil dokumentasi foto serah terima. Setelah status <b>Selesai Diterima</b>, tanda terima resmi tersimpan di sistem.", styles['TableCell'])],
    ]
    dist_table = Table(dist_guide_data, colWidths=[120, 385])
    dist_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(dist_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Kewajiban Pemeriksaan Fisik Barang Sebelum Mengonfirmasi",
        "Pastikan kondisi fisik barang yang diserahkan dalam keadaan baik dan tidak cacat. "
        "Bila ada ketidaksesuaian barang, sampaikan langsung kepada petugas PIC Distribusi di lokasi untuk penukaran barang pengganti.",
        kind="warning",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 11: BAB IX - PANDUAN PEMECAHAN MASALAH (FAQ) & HELPDESK
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB IX: PANDUAN PEMECAHAN MASALAH (FAQ) & MEJA BANTUAN TU", styles))
    
    story.append(Paragraph("9.1 Tanya Jawab Operasional Pegawai (Frequently Asked Questions)", styles['SubChapterHeading']))

    faq_list = [
        (
            "T: Berapa batas maksimal barang yang boleh diajukan oleh seorang pegawai?",
            "J: Pengajuan personal disesuaikan dengan kebutuhan wajar penggunaan dinas harian staf (misal: 1 s.d. 2 rim kertas per bulan). "
            "Untuk kebutuhan dalam skala besar rapat divisi, disarankan diajukan melalui akun Subdirektorat (Pemohon Kolektif)."
        ),
        (
            "T: Mengapa barang yang disetujui Kasubag TU jumlahnya lebih sedikit dari yang diajukan?",
            "J: Kasubag TU melakukan rasionalisasi kuota berdasarkan sisa fisik persediaan gudang agar distribusi ATK merata "
            "ke seluruh pegawai dan seksi di lingkungan Kementerian PKP RI."
        ),
        (
            "T: Apa yang harus dilakukan bila pengajuan permohonan berstatus 'Ditolak'?",
            "J: Buka detail tiket dan baca alasan penolakan dari Kasubag TU. Anda dapat memperbaiki spesifikasi atau mengajukan "
            "kembali dengan kuantitas yang disesuaikan."
        ),
        (
            "T: Lupa kata sandi akun dan email kantor sedang sulit diakses, bagaimana cara masuk?",
            "J: Buka menu 'Lupa Password' di halaman login. Cukup masukkan NIP 18 digit resmi Anda dan ketikkan kata sandi baru. "
            "Sistem ATEKA tidak memerlukan verifikasi email SMTP, sehingga sandi baru langsung aktif saat itu juga."
        ),
        (
            "T: Apakah staf boleh mengajukan ATK baru yang belum pernah ada di kantor sebelumnya?",
            "J: Boleh, gunakan menu 'Permintaan Pengadaan (Beli Baru)'. Tuliskan nama barang dan alasan kebutuhan kerja dinas "
            "agar dapat diverifikasi dan dibelanjakan ke supplier oleh Subbag TU."
        ),
    ]

    for q, a in faq_list:
        story.append(Paragraph(f"<b>{q}</b>", styles['SubSubHeading']))
        story.append(Paragraph(a, styles['BodyJustify']))
        story.append(Spacer(1, 1))

    story.append(Spacer(1, 4))
    story.append(Paragraph("9.2 Saluran Bantuan Teknis & Meja Bantuan Subbagian Tata Usaha", styles['SubChapterHeading']))

    help_data = [
        [Paragraph("<b>Unit Pengelola Sistem</b>", styles['TableCellBold']), Paragraph(": Subbagian Tata Usaha, Direktorat Jenderal Perumahan, Kementerian PKP RI", styles['TableCell'])],
        [Paragraph("<b>Lokasi Kantor Logistik</b>", styles['TableCellBold']), Paragraph(": Ruang Tata Usaha & Gudang Logistik ATK, Gedung Kementerian PKP RI, Jakarta", styles['TableCell'])],
        [Paragraph("<b>Petugas Penghubung</b>", styles['TableCellBold']), Paragraph(": Pengelola Inventori Barang & Tim PIC Distribusi Gudang", styles['TableCell'])],
        [Paragraph("<b>Jam Operasional Pelayanan</b>", styles['TableCellBold']), Paragraph(": Senin &ndash; Jumat, Pukul 08.30 &ndash; 16.00 WIB (Hari Kerja Dinas)", styles['TableCell'])],
        [Paragraph("<b>Akses Layanan Portal</b>", styles['TableCellBold']), Paragraph(": Portal ATEKA Beroperasi 24 Jam Mandiri untuk Pengajuan Daring Pegawai", styles['TableCell'])],
    ]
    help_table = Table(help_data, colWidths=[130, 375])
    help_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), C_BG_LIGHT),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 6),
        ('RIGHTPADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(help_table)

    story.append(Spacer(1, 8))
    story.append(make_callout(
        "Komitmen Tata Kelola Persediaan yang Akuntabel & Efisien",
        "Gunakan fasilitas alat tulis kantor secara bijak dan bertanggung jawab guna mendukung kinerja prima Kementerian Perumahan "
        "dan Kawasan Permukiman RI dalam melayani masyarakat Indonesia.",
        kind="info",
        styles=styles,
        width=505
    ))

    return story


# ==============================================================================
# 5. FUNGSI EKSEKUSI PEMBUATAN PDF
# ==============================================================================
def generate_pdf(output_filename="Manual_Book_Ateka_Pegawai.pdf"):
    print(f"[*] Menyiapkan pembuatan PDF: {output_filename}")
    
    base_dir = os.path.dirname(os.path.abspath(__file__))
    logo_path = os.path.join(base_dir, "public", "hope-ui", "assets", "images", "auth", "kemenpkp-logo.png")
    img_dir = os.path.join(base_dir, "assets", "manual_book_pegawai_images")
    pdf_path = os.path.join(base_dir, output_filename)

    # Konfigurasi A4: 595.27 x 842 pt. Margin 45 pt kiri/kanan -> printable width 505.27 pt
    doc = SimpleDocTemplate(
        pdf_path,
        pagesize=A4,
        leftMargin=45,
        rightMargin=45,
        topMargin=46,
        bottomMargin=46,
        title="Buku Panduan Pengguna ATEKA - Role Pegawai",
        author="Subbagian Tata Usaha Kementerian PKP RI",
        subject="User Manual Book Role Pegawai (Pemohon Individu)",
        creator="Sistem Inventori ATEKA Kementerian PKP RI"
    )

    styles = create_custom_styles()
    story = build_manual_book_story(styles, logo_path, img_dir)

    print(f"[*] Membangun dokumen dengan {len(story)} elemen Platypus...")
    doc.build(story, canvasmaker=AtekaNumberedCanvas)
    
    file_size_kb = os.path.getsize(pdf_path) / 1024
    print(f"[SUCCESS] PDF Pegawai berhasil diterbitkan: {pdf_path} ({file_size_kb:.2f} KB)")
    return pdf_path


if __name__ == "__main__":
    out_file = sys.argv[1] if len(sys.argv) > 1 else "Manual_Book_Ateka_Pegawai.pdf"
    generate_pdf(out_file)
