"""
Script Pembuat Buku Panduan Pengguna (User Manual Book)
Role Subdirektorat (Pemohon Kolektif) - Sistem ATEKA Kementerian PKP RI
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
            self.drawRightString(595 - 45, 842 - 30, "PANDUAN ATEKA • ROLE SUBDIREKTORAT")
            
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
            self.drawString(45, 25, "Buku Panduan Subdirektorat (Pemohon Kolektif) • Edisi Terintegrasi 2026")
            
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
        icon_label = "TIPS OPERASIONAL SUBDIREKTORAT"
    elif kind == "warning":
        bg_col = C_AMBER_BG
        border_col = C_AMBER
        icon_label = "PERHATIAN KHUSUS"
    elif kind == "success":
        bg_col = C_EMERALD_BG
        border_col = C_EMERALD
        icon_label = "LOGIKA SISTEM TERPADU"
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
# 4. KONTEN DOKUMEN BUKU PANDUAN PENGGUNA (ROLE SUBDIREKTORAT)
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
    story.append(Paragraph("ROLE SUBDIREKTORAT (PEMOHON KOLEKTIF UNIT KERJA)", styles['CoverRole']))

    story.append(Paragraph(
        "Panduan Komprehensif Tata Kelola Permohonan ATK Kolektif Subdirektorat, "
        "Eksplorasi E-Katalog Gudang, Pengusulan Belanja Pengadaan Barang Baru, "
        "serta Pelacakan Verifikasi Kasubag TU & Prosedur Serah Terima Fisik.",
        styles['CoverAppDesc']
    ))

    meta_data = [
        [Paragraph("<b>Nama Aplikasi</b>", styles['TableCellBold']), Paragraph(": ATEKA (Aplikasi Tata Kelola Persediaan & Pengadaan ATK)", styles['TableCell'])],
        [Paragraph("<b>Sasaran Pengguna</b>", styles['TableCellBold']), Paragraph(": Staf & Penanggung Jawab Subdirektorat (SDK, PJF, KBR, Subbag TU)", styles['TableCell'])],
        [Paragraph("<b>Klasifikasi Dokumen</b>", styles['TableCellBold']), Paragraph(": Panduan Operasional Kedinasan (Internal Kementerian PKP RI)", styles['TableCell'])],
        [Paragraph("<b>Versi Rilis Sistem</b>", styles['TableCellBold']), Paragraph(": Versi 2.0 (Edisi Khusus Pemohon Kolektif Subdirektorat - 2026)", styles['TableCell'])],
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
    story.extend(make_header_bar("KATA PENGANTAR & PROFIL PERAN SUBDIREKTORAT", styles))
    
    story.append(Paragraph(
        "<b>Sistem Inventori ATEKA</b> merupakan portal digital resmi Kementerian Perumahan dan Kawasan Permukiman RI "
        "yang mengintegrasikan seluruh siklus pengelolaan alat tulis kantor secara transparan dan akuntabel. "
        "<b>Role Subdirektorat (Pemohon Kolektif)</b> memiliki kedudukan strategis untuk menghimpun seluruh kebutuhan ATK "
        "di lingkup satuan kerjanya dalam satu berkas terpadu, guna mencegah pemborosan dan mempercepat distribusi logistik kantor.",
        styles['BodyJustify']
    ))

    story.append(make_callout(
        "Empat Satuan Unit Kerja Resmi Pemohon Kolektif Subdirektorat",
        "Setiap akun Subdirektorat mewakili salah satu dari 4 entitas resmi di bawah naungan Ditjen Perumahan:<br/>"
        "1. <b>Subdirektorat SDK</b> : Subdirektorat Sumber Daya Konstruksi Perumahan dan Kawasan Permukiman<br/>"
        "2. <b>Subdirektorat PJF</b> : Subdirektorat Pengelolaan Jabatan Fungsional Perumahan dan Pengembangan Profesi<br/>"
        "3. <b>Subdirektorat KBR</b> : Subdirektorat Kendala Bangunan Rumah<br/>"
        "4. <b>Subbagian TU</b> : Subbagian Tata Usaha",
        kind="info",
        styles=styles,
        width=505
    ))

    story.append(Spacer(1, 4))
    story.append(Paragraph("Matriks Perbandingan Wewenang: Subdirektorat vs Pegawai Individu", styles['SubChapterHeading']))
    comp_data = [
        [Paragraph("<b>Parameter</b>", styles['TableHeader']), Paragraph("<b>Subdirektorat (Pemohon Kolektif)</b>", styles['TableHeader']), Paragraph("<b>Pegawai (Pemohon Individu)</b>", styles['TableHeader'])],
        [Paragraph("<b>Tujuan Pengajuan</b>", styles['TableCellBold']), Paragraph("Kebutuhan operasional bersama satu seksi/unit kerja", styles['TableCell']), Paragraph("Kebutuhan harian personal staf pegawai", styles['TableCell'])],
        [Paragraph("<b>Skala Volume</b>", styles['TableCellBold']), Paragraph("Jumlah sedang hingga besar (misal: 10 rim kertas, 5 dos map)", styles['TableCell']), Paragraph("Jumlah kecil terbatas (misal: 1 rim kertas, 2 pulpen)", styles['TableCell'])],
        [Paragraph("<b>Pilihan Unit Kerja</b>", styles['TableCellBold']), Paragraph("Wajib memilih 1 dari 4 Subdirektorat resmi saat daftar", styles['TableCell']), Paragraph("Terdaftar sebagai pegawai umum perorangan", styles['TableCell'])],
        [Paragraph("<b>Usulan Belanja Baru</b>", styles['TableCellBold']), Paragraph("Dapat mengusulkan pengadaan barang khusus (REQ-BUY)", styles['TableCell']), Paragraph("Terbatas pada persediaan fisik di gudang", styles['TableCell'])],
        [Paragraph("<b>Bukti Serah Terima</b>", styles['TableCellBold']), Paragraph("Berita acara resmi berkop dinas kementerian per unit", styles['TableCell']), Paragraph("Tanda terima distribusi perseorangan", styles['TableCell'])],
    ]
    comp_table = Table(comp_data, colWidths=[95, 210, 200])
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
        [Paragraph("<b>Bab I</b>", styles['TableCellBold']), Paragraph("<b>Registrasi Akun Subdirektorat & Pemilihan Unit Kerja</b>", styles['TableCell']), Paragraph("image1.png (Mobile Form)", styles['TableCellCenter']), Paragraph("Hal 3", styles['TableCellCenter'])],
        [Paragraph("<b>Bab II</b>", styles['TableCellBold']), Paragraph("<b>Login Sistem & Prosedur Lupa Kata Sandi Berbasis NIP</b>", styles['TableCell']), Paragraph("image2.png & image3.png", styles['TableCellCenter']), Paragraph("Hal 4", styles['TableCellCenter'])],
        [Paragraph("<b>Bab III</b>", styles['TableCellBold']), Paragraph("<b>Eksplorasi Dashboard & 4 Indikator Status Permintaan</b>", styles['TableCell']), Paragraph("image4.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 5", styles['TableCellCenter'])],
        [Paragraph("<b>Bab IV</b>", styles['TableCellBold']), Paragraph("<b>E-Katalog ATK & Pengecekan Ketersediaan Stok Gudang</b>", styles['TableCell']), Paragraph("image8.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 6", styles['TableCellCenter'])],
        [Paragraph("<b>Bab V</b>", styles['TableCellBold']), Paragraph("<b>Formulir Permintaan ATK dari Persediaan Gudang</b>", styles['TableCell']), Paragraph("image6.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 7", styles['TableCellCenter'])],
        [Paragraph("<b>Bab VI</b>", styles['TableCellBold']), Paragraph("<b>Usulan Pengadaan Barang ATK Baru (Belanja Supplier)</b>", styles['TableCell']), Paragraph("image7.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 8", styles['TableCellCenter'])],
        [Paragraph("<b>Bab VII</b>", styles['TableCellBold']), Paragraph("<b>Pemantauan Riwayat Pengajuan & Catatan Kasubag TU</b>", styles['TableCell']), Paragraph("image5.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 9", styles['TableCellCenter'])],
        [Paragraph("<b>Bab VIII</b>", styles['TableCellBold']), Paragraph("<b>Pusat Notifikasi & Prosedur Serah Terima Fisik Barang</b>", styles['TableCell']), Paragraph("image9.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 10", styles['TableCellCenter'])],
        [Paragraph("<b>Bab IX</b>", styles['TableCellBold']), Paragraph("<b>Panduan Pemecahan Masalah (FAQ) & Layanan Helpdesk</b>", styles['TableCell']), Paragraph("FAQ & Alur Eskalasi", styles['TableCellCenter']), Paragraph("Hal 11", styles['TableCellCenter'])],
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
    # HALAMAN 3: BAB I - REGISTRASI AKUN SUBDIREKTORAT (image1.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB I: REGISTRASI AKUN SUBDIREKTORAT & PEMILIHAN UNIT", styles))
    
    story.append(Paragraph(
        "Modul pendaftaran akun mandiri memungkinkan operator atau penanggung jawab ATK unit kerja untuk mendaftarkan akun "
        "resmi subdirektorat ke dalam portal ATEKA. Form pendaftaran dilengkapi validasi NIP dan pemilihan divisi dinamis.",
        styles['BodyJustify']
    ))

    # Layout 2 Kolom: Kiri Screenshot Mobile (image1.png), Kanan Langkah & Form Detail
    img1_path = os.path.join(img_dir, "image1.png")
    # Asli: 392 x 795 (rasio 0.493) -> lebar 170, tinggi 345
    img1_box = make_screenshot_box(img1_path, 170, 345, "Form Registrasi Akun Subdit", styles)

    reg_steps = [
        make_step_row(1, "Akses Halaman Pendaftaran", "Buka browser, akses <b>/register</b> atau klik <i>'Daftar di Sini'</i> pada halaman Login.", styles),
        make_step_row(2, "Isi Nama Lengkap & Gelar", "Ketik nama lengkap penanggung jawab. Kolom <b>Gelar Akademik (Opsional)</b> dapat diisi gelar seperti <i>S.T., M.Si.</i> untuk berita acara resmi.", styles),
        make_step_row(3, "Isi Email Kedinasan", "Gunakan email dinas aktif untuk identitas login akun unit kerja Anda.", styles),
        make_step_row(4, "Masukkan NIP 18 Digit", "Input NIP resmi tanpa spasi atau dengan spasi. NIP wajib diisi sebagai instrumen autentikasi dan pemulihan kata sandi mandiri.", styles),
        make_step_row(5, "Pilih Jenis: 'Subdirektorat'", "Klik radio button <b>Subdirektorat</b> dengan badge <i>'Pemohon Kolektif'</i>. <u>Penting:</u> Jangan memilih Pegawai jika mewakili unit kerja.", styles),
        make_step_row(6, "Pilih Dropdown Unit Kerja Resmi", "Dropdown dinamis <b>'Pilih Subdirektorat / Unit Kerja'</b> akan muncul otomatis. Pilih salah satu: <i>Subdirektorat SDK, Subdirektorat PJF, Subdirektorat KBR,</i> atau <i>Subbagian TU</i>.", styles),
        make_step_row(7, "Tentukan Kata Sandi & Konfirmasi", "Ketikkan password minimal 6 karakter kombinasi, lalu ulangi pada kolom konfirmasi.", styles),
        make_step_row(8, "Klik 'Daftarkan Akun'", "Sistem otomatis melakukan validasi dan <b>Login Instan</b> ke Dashboard ATEKA tanpa perlu verifikasi email rumit.", styles),
    ]

    col_right_content = reg_steps
    reg_table = Table([[img1_box, col_right_content]], colWidths=[185, 320])
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
        "Ketentuan Integritas NIP & Satu Akun Kolektif per Operator Unit",
        "Nomor Induk Pegawai (NIP) yang didaftarkan terikat secara unik dalam database sistem. "
        "Pastikan operator yang ditunjuk mengoordinasikan pendaftaran agar akun kolektif unit subdirektorat tidak terduplikasi.",
        kind="warning",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 4: BAB II - LOGIN & LUPA KATA SANDI MANDIRI (image2.png & image3.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB II: LOGIN SISTEM & PEMULIHAN KATA SANDI BERBASIS NIP", styles))
    
    story.append(Paragraph(
        "Akses masuk portal ATEKA dirancang dengan standar keamanan tinggi dan kemudahan autentikasi. "
        "Apabila pengguna mengalami lupa kata sandi, sistem menyediakan fitur reset mandiri seketika berbasis validasi NIP.",
        styles['BodyJustify']
    ))

    img2_path = os.path.join(img_dir, "image2.png")
    # Asli 379 x 770 -> lebar 130, tinggi 264
    img2_box = make_screenshot_box(img2_path, 130, 264, "Form Login Masuk Sistem", styles)

    img3_path = os.path.join(img_dir, "image3.png")
    # Asli 477 x 969 -> lebar 130, tinggi 264
    img3_box = make_screenshot_box(img3_path, 130, 264, "Form Reset Kata Sandi NIP", styles)

    # Panel Kiri: Login
    login_panel = [
        Paragraph("<b>2.1 Masuk ke Akun Anda (/login)</b>", styles['SubChapterHeading']),
        img2_box,
        Spacer(1, 3),
        Paragraph("<b>Langkah Autentikasi Masuk:</b>", styles['StepTitle']),
        Paragraph("&bull; Masukkan alamat <b>Email Kedinasan</b> yang telah terdaftar.<br/>"
                  "&bull; Masukkan <b>Kata Sandi (Password)</b> akun Anda.<br/>"
                  "&bull; Centang <i>'Ingat Saya'</i> pada perangkat kerja dinas.<br/>"
                  "&bull; Klik tombol biru <b>'Masuk ke Akun'</b>.<br/>"
                  "&bull; Tersedia tombol <i>Akses Cepat Demo</i> untuk keperluan bimbingan teknis & sosialisasi aplikasi.", styles['BulletText']),
    ]

    # Panel Kanan: Lupa Password
    lupa_panel = [
        Paragraph("<b>2.2 Reset Sandi Mandiri (/lupa-password)</b>", styles['SubChapterHeading']),
        img3_box,
        Spacer(1, 3),
        Paragraph("<b>Langkah Pemulihan Kata Sandi:</b>", styles['StepTitle']),
        Paragraph("&bull; Klik tautan <b>'Lupa Password?'</b> pada form login.<br/>"
                  "&bull; Masukkan <b>NIP 18 Digit</b> resmi yang terdaftar.<br/>"
                  "&bull; Masukkan <b>Kata Sandi Baru</b> (minimal 6 karakter).<br/>"
                  "&bull; Ketik ulang pada <b>Konfirmasi Kata Sandi</b>.<br/>"
                  "&bull; Klik <b>'Perbarui Kata Sandi'</b>. Kata sandi aktif seketika tanpa perlu menunggu tautan email SMTP!", styles['BulletText']),
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
        "Keunggulan Mekanisme Reset Sandi Berbasis Validasi NIP",
        "Mekanisme reset via NIP mengatasi kendala kegagalan pengiriman email yang sering dialami jaringan kedinasan berpembatas firewall. "
        "Pastikan NIP yang dimasukkan persis sama dengan data yang didaftarkan pada Bab I.",
        kind="tip",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 5: BAB III - EKSPLORASI DASHBOARD SUBDIREKTORAT (image4.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB III: EKSPLORASI DASHBOARD & 4 INDIKATOR STATUS PERMINTAAN", styles))
    
    story.append(Paragraph(
        "Dashboard merupakan pusat navigasi operasional Subdirektorat. Halaman ini menyajikan ucapan selamat datang personal, "
        "banner akses cepat, filter periode anggaran, 4 kartu indikator KPI dokumen, serta tabel riwayat transaksi terkini.",
        styles['BodyJustify']
    ))

    img4_path = os.path.join(img_dir, "image4.png")
    # Asli 1255 x 721 -> lebar 435, tinggi 250
    img4_box = make_screenshot_box(img4_path, 435, 250, "Antarmuka Dashboard Subdirektorat SDK (Sistem ATEKA)", styles)
    story.append(img4_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Komponen Antarmuka Utama & Penjelasan 4 Indikator KPI", styles['SubChapterHeading']))

    dash_guide_data = [
        [Paragraph("<b>Elemen Dashboard</b>", styles['TableHeader']), Paragraph("<b>Tampilan pada Screenshot (Gambar di Atas) & Fungsinya</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Header Identitas</b>", styles['TableCellBold']), Paragraph("Menampilkan nama pengguna (contoh: <i>Fairuz Armina Nur Azizah SDK</i>) disertai badge status <i>'Subdirektorat (Pemohon Kolektif)'</i> dan unit kerja terdaftar.", styles['TableCell'])],
        [Paragraph("<b>2. Quick Actions</b>", styles['TableCellBold']), Paragraph("Tombol pintas instan: <b>'+ Buat Pengajuan Baru'</b> (menuju form pengajuan), <b>'Katalog ATK'</b>, dan <b>'Riwayat Pengajuan'</b>.", styles['TableCell'])],
        [Paragraph("<b>3. Filter Periode</b>", styles['TableCellBold']), Paragraph("Bilah pemilih Bulan dan Tahun untuk menyaring ringkasan statistik permohonan ATK sesuai kalender tahun anggaran berjalan.", styles['TableCell'])],
        [Paragraph("<b>4. Empat Kartu KPI</b>", styles['TableCellBold']), Paragraph("<b>Kuning (Menunggu Review TU):</b> Tiket baru dikirimkan.<br/>"
                                                                                 "<b>Biru (Disetujui TU):</b> Disetujui Kasubag TU untuk penyerahan atau pengadaan.<br/>"
                                                                                 "<b>Hijau (Selesai Diterima):</b> Fisik barang telah diserahkan oleh PIC Distribusi.<br/>"
                                                                                 "<b>Merah (Ditolak):</b> Berkas tidak disetujui disertai alasan tertulis resmi.", styles['TableCell'])],
        [Paragraph("<b>5. Banner Pengadaan</b>", styles['TableCellBold']), Paragraph("Kotak pemberitahuan ketersediaan hak usulan pengadaan barang baru jika persediaan gudang habis atau belum terdaftar.", styles['TableCell'])],
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
        "Akurasi Monitoring Real-Time",
        "Setiap perubahan status yang dilakukan oleh Kasubag TU maupun PIC Gudang akan langsung memperbarui angka pada ke-4 kartu KPI ini "
        "secara real-time tanpa perlu me-refresh halaman berulang kali.",
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
        "E-Katalog ATEKA merupakan etalase digital yang mencerminkan fisik stok barang di ruang penyimpanan gudang Subbag TU. "
        "Pemohon kolektif diimbau meninjau katalog sebelum mengajukan permintaan agar mengetahui ketersediaan riil barang.",
        styles['BodyJustify']
    ))

    img8_path = os.path.join(img_dir, "image8.png")
    # Asli 2282 x 1310 -> lebar 435, tinggi 250
    img8_box = make_screenshot_box(img8_path, 435, 250, "Katalog Alat Tulis Kantor & Indikator Persediaan Gudang", styles)
    story.append(img8_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Eksplorasi Fitur Katalog & Cara Membaca Visual Kartu Barang", styles['SubChapterHeading']))

    katalog_features = [
        [Paragraph("<b>Fitur & Elemen</b>", styles['TableHeader']), Paragraph("<b>Penjelasan Fungsional & Panduan Penggunaan</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Bilah Pencarian Cepat</b>", styles['TableCellBold']), Paragraph("Ketik nama atau kode barang pada kolom pencarian (contoh: <i>Map Coklat, Pulpen Gel, Kertas A4</i>) untuk memfilter item seketika.", styles['TableCell'])],
        [Paragraph("<b>2. Filter Kategori ATK</b>", styles['TableCellBold']), Paragraph("Pilih kategori spesifik (Kertas & Buku, Pengarsipan, Alat Tulis, Perekat, Aksesoris Meja) guna mempersempit daftar barang.", styles['TableCell'])],
        [Paragraph("<b>3. Kartu Barang Interaktif</b>", styles['TableCellBold']), Paragraph("Setiap kartu menyajikan foto produk, nama barang, kode inventori resmi, sisa unit fisik gudang, dan satuan barang.", styles['TableCell'])],
        [Paragraph("<b>4. Indikator Meter Stok</b>", styles['TableCellBold']), Paragraph("<b>Hijau (Tersedia):</b> Stok > 10 unit, aman diajukan.<br/>"
                                                                                   "<b>Kuning (Menipis):</b> Stok 1 s.d. 10 unit, segera ajukan sebelum habis.<br/>"
                                                                                   "<b>Merah (Habis):</b> Stok 0 unit di gudang.", styles['TableCell'])],
        [Paragraph("<b>5. Tombol 'Ajukan Barang'</b>", styles['TableCellBold']), Paragraph("Mengklik tombol ini langsung mengarahkan Anda ke formulir permohonan dengan item tersebut sudah otomatis terpilih.", styles['TableCell'])],
        [Paragraph("<b>6. Restock Instan (Stok 0)</b>", styles['TableCellBold']), Paragraph("Bila barang habis, muncul tombol <b>'Pesan Barang Ini (Restock)'</b> untuk langsung membuat tiket belanja pengadaan baru (REQ-BUY).", styles['TableCell'])],
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
        "Efisiensi Pemesanan Langsung dari E-Katalog",
        "Mengajukan barang langsung melalui kartu katalog meminimalisir kesalahan penulisan nama atau spesifikasi barang "
        "karena data otomatis terkoneksi dengan master inventori gudang Kementerian PKP RI.",
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
        "Jalur formulir ini digunakan saat Subdirektorat memerlukan alat tulis kantor yang <b>stok fisiknya telah tersedia di gudang</b>. "
        "Formulir mendukung pengajuan banyak item barang sekaligus (multi-item) dalam satu nomor tiket pengajuan kolektif.",
        styles['BodyJustify']
    ))

    img6_path = os.path.join(img_dir, "image6.png")
    # Asli 1379 x 792 -> lebar 435, tinggi 250
    img6_box = make_screenshot_box(img6_path, 435, 250, "Formulir Buat Pengajuan Baru - Permintaan Stok Gudang", styles)
    story.append(img6_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Panduan Langkah Pengisian Formulir Permintaan Persediaan", styles['SubChapterHeading']))

    pengajuan_steps_data = [
        [Paragraph("<b>Tahapan Pengisian</b>", styles['TableHeader']), Paragraph("<b>Rincian Tindakan pada Antarmuka (Gambar di Atas)</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Jenis Permintaan</b>", styles['TableCellBold']), Paragraph("Pastikan dropdown terpilih <b>'Subdirektorat (Kolektif Unit Kerja)'</b> agar tercatat atas nama unit resmi Anda.", styles['TableCell'])],
        [Paragraph("<b>2. Catatan Keperluan</b>", styles['TableCellBold']), Paragraph("Tuliskan tujuan penggunaan dinas (contoh: <i>'Kebutuhan administrasi berkas rapat koordinasi pembangunan perumahan'</i>).", styles['TableCell'])],
        [Paragraph("<b>3. Pilih Barang Gudang</b>", styles['TableCellBold']), Paragraph("Pilih barang dari dropdown (contoh pada screenshot: <i>Map Coklat</i>). Sistem otomatis menampilkan info sisa fisik: <b>Sisa Stok: 5 Pcs</b>.", styles['TableCell'])],
        [Paragraph("<b>4. Tentukan Kuantitas</b>", styles['TableCellBold']), Paragraph("Ketik jumlah yang dibutuhkan. Sistem mencegah input melebihi angka stok gudang yang tersedia.", styles['TableCell'])],
        [Paragraph("<b>5. Tambah Item Lainnya</b>", styles['TableCellBold']), Paragraph("Klik tombol hijau <b>'+ Tambah Barang Lain'</b> untuk mengajukan item kedua, ketiga, dst. dalam satu lembar tiket yang sama.", styles['TableCell'])],
        [Paragraph("<b>6. Tombol Kirim</b>", styles['TableCellBold']), Paragraph("Klik tombol biru <b>'Kirim Pengajuan ke Kasubag TU'</b>. Sistem langsung menerbitkan kode tiket resmi <b>REQ-YYYYMMDD-XXXX</b>.", styles['TableCell'])],
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
        "Logika Cerdas: Pengalihan Otomatis ke Alur Beli Baru (REQ-BUY)",
        "Bila barang yang Anda pilih ternyata stok gudangnya mendadak habis atau kuantitas permintaan melampaui sisa gudang, "
        "sistem <b>secara otomatis</b> mengonversi tiket menjadi permohonan pengadaan baru (<b>REQ-BUY-...</b>) tanpa perlu input ulang!",
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
        "Formulir ini dikhususkan bagi Subdirektorat yang membutuhkan perlengkapan kantor atau ATK khusus yang <b>belum tersedia "
        "dalam katalog master persediaan gudang</b>, sehingga memerlukan pembelanjaan baru ke rekanan/supplier resmi.",
        styles['BodyJustify']
    ))

    img7_path = os.path.join(img_dir, "image7.png")
    # Asli 2282 x 1310 -> lebar 435, tinggi 250
    img7_box = make_screenshot_box(img7_path, 435, 250, "Formulir Permintaan Pengadaan Barang ATK Baru (Beli Baru)", styles)
    story.append(img7_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Panduan Pengisian Formulir Pengadaan & Pemilihan Satuan Fleksibel", styles['SubChapterHeading']))

    pengadaan_guide_data = [
        [Paragraph("<b>Field Formulir</b>", styles['TableHeader']), Paragraph("<b>Ketentuan Pengisian & Fitur Khusus (Tampak pada Screenshot)</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Box Alur Pembelian</b>", styles['TableCellBold']), Paragraph("Banner biru mengingatkan bahwa barang yang diajukan akan diproses oleh Kasubag TU untuk dibelanjakan ke supplier pihak ketiga.", styles['TableCell'])],
        [Paragraph("<b>2. Alasan Pengadaan</b>", styles['TableCellBold']), Paragraph("Tuliskan justifikasi urgensi kegiatan dinas (misal: <i>'Kebutuhan map akreditasi khusus dan kertas sertifikat kegiatan lapangan'</i>).", styles['TableCell'])],
        [Paragraph("<b>3. Nama Barang Baru</b>", styles['TableCellBold']), Paragraph("Ketikkan nama barang. Dilengkapi <i>autocomplete rekomendasi</i> yang memberi saran nama barang standar saat Anda mengetik.", styles['TableCell'])],
        [Paragraph("<b>4. Satuan Khusus (18 Opsi)</b>", styles['TableCellBold']), Paragraph("Pilih satuan yang paling tepat dari dropdown: <b>Pcs, Rim, Dos/Dus, Pak, Box, Lusin, Buah, Lembar, Buku, Set, Roll, Kotak, Batang, Tube, Bungkus, Botol, Unit, atau Meter</b>.", styles['TableCell'])],
        [Paragraph("<b>5. Spesifikasi Teknis</b>", styles['TableCellBold']), Paragraph("Cantumkan merk rekomendasi, warna, ukuran, atau ketebalan (contoh: <i>'Folio F4 80gsm warna biru dongker'</i>) agar pembelanjaan akurat.", styles['TableCell'])],
        [Paragraph("<b>6. Kode Tiket REQ-BUY</b>", styles['TableCellBold']), Paragraph("Setelah dikirim, tiket diterbitkan dengan format <b>REQ-BUY-YYYYMMDD-XXXX</b> dan masuk dalam antrean Kasubag TU.", styles['TableCell'])],
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
        "Batas Waktu Pengajuan Usulan Pengadaan",
        "Mengingat proses belanja ke penyedia membutuhkan waktu verifikasi anggaran dan kurir pengiriman, ajukan usulan "
        "minimal 3 s.d. 5 hari kerja sebelum tanggal pelaksanaan rapat atau kegiatan lapangan unit Anda.",
        kind="warning",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 9: BAB VII - RIWAYAT PENGAJUAN & CATATAN KASUBAG TU (image5.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB VII: PEMANTAUAN RIWAYAT PENGAJUAN & CATATAN KASUBAG TU", styles))
    
    story.append(Paragraph(
        "Menu Riwayat Pengajuan menyajikan pelacakan transparan seluruh permohonan yang telah diajukan oleh Subdirektorat. "
        "Di sini Anda dapat melihat kuantitas hasil verifikasi Kasubag TU serta membaca catatan resmi pimpinan.",
        styles['BodyJustify']
    ))

    img5_path = os.path.join(img_dir, "image5.png")
    # Asli 1379 x 792 -> lebar 435, tinggi 250
    img5_box = make_screenshot_box(img5_path, 435, 250, "Tabel Riwayat Pengajuan & Catatan Verifikasi Kasubag TU", styles)
    story.append(img5_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Membaca Komponen Kolom Tabel & Catatan Verifikasi", styles['SubChapterHeading']))

    riwayat_guide_data = [
        [Paragraph("<b>Kolom Tabel</b>", styles['TableHeader']), Paragraph("<b>Penjelasan Informasi yang Ditampilkan (Sesuai Gambar Screenshot)</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Kode & Tipe Tiket</b>", styles['TableCellBold']), Paragraph("Menampilkan kode unik tiket (contoh: <b>REQ-BUY-20261011-0005</b>), waktu pengiriman, serta badge biru <i>'Kolektif Subdit'</i>.", styles['TableCell'])],
        [Paragraph("<b>2. Barang yang Diminta</b>", styles['TableCellBold']), Paragraph("Menyajikan daftar nama barang beserta kuantitas diajukan vs <b>kuantitas disetujui TU</b> jika terjadi penyesuaian kuota.", styles['TableCell'])],
        [Paragraph("<b>3. Catatan Kasubag TU</b>", styles['TableCellBold']), Paragraph("Pesan resmi dari Kasubag TU (contoh pada screenshot: <i>'Perlu konfirmasi anggaran dan sisa stok kuota'</i>) untuk transparansi keputusan.", styles['TableCell'])],
        [Paragraph("<b>4. Label Status Dinamis</b>", styles['TableCellBold']), Paragraph("Menunjukkan status terkini: <b>Pending (Kuning)</b> dalam review, <b>Disetujui (Biru)</b>, <b>Selesai (Hijau)</b>, atau <b>Ditolak (Merah)</b>.", styles['TableCell'])],
        [Paragraph("<b>5. Tombol 'Detail'</b>", styles['TableCellBold']), Paragraph("Membuka pop-up modal rincian lengkap berkas permohonan, riwayat alur persetujuan, dan waktu pemrosesan dokumen.", styles['TableCell'])],
        [Paragraph("<b>6. Tombol 'Bukti'</b>", styles['TableCellBold']), Paragraph("Tombol ini aktif saat status <b>Selesai Diserahkan</b>, berfungsi mencetak Berita Acara Tanda Terima resmi berkop kementerian.", styles['TableCell'])],
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
        "Menindaklanjuti Catatan Verifikasi Kasubag TU",
        "Apabila pimpinan TU memberikan catatan penyesuaian kuota atau menolak tiket karena pembatasan anggaran bulanan, "
        "operator Subdirektorat dapat berkoordinasi langsung dengan Subbag TU atau mengajukan revisi permohonan baru.",
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
        "ATEKA dilengkapi sistem notifikasi terintegrasi yang memberitahu operator ketika status pengajuan berubah atau saat "
        "barang pesanan telah tiba di gudang. Bab ini menjelaskan alur penyerahan fisik barang hingga pencetakan tanda terima.",
        styles['BodyJustify']
    ))

    img9_path = os.path.join(img_dir, "image9.png")
    # Asli 2282 x 1310 -> lebar 435, tinggi 250
    img9_box = make_screenshot_box(img9_path, 435, 250, "Pusat Notifikasi Navbar - Pemberitahuan Ketersediaan Barang", styles)
    story.append(img9_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Mekanisme Notifikasi & Prosedur Serah Terima Fisik Barang", styles['SubChapterHeading']))

    dist_guide_data = [
        [Paragraph("<b>Tahapan Prosedur</b>", styles['TableHeader']), Paragraph("<b>Penjelasan Teknis & Aksi Pengguna (Sesuai Screenshot di Atas)</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Lonceng Notifikasi</b>", styles['TableCellBold']), Paragraph("Ikon lonceng di navbar kanan atas menampilkan badge angka merah ketika ada pemberitahuan baru (contoh: 2 notifikasi belum dibaca).", styles['TableCell'])],
        [Paragraph("<b>2. Pesan 'Barang Tersedia'</b>", styles['TableCellBold']), Paragraph("Pop-up menampilkan notif: <i>'Pengadaan Barang Telah Tersedia'</i> untuk tiket Anda. Ini menandakan barang siap diambil atau diserahkan.", styles['TableCell'])],
        [Paragraph("<b>3. Penyiapan oleh PIC</b>", styles['TableCellBold']), Paragraph("Petugas gudang (PIC Distribusi) menyiapkan paket fisik barang sesuai jumlah yang disetujui dalam sistem ATEKA.", styles['TableCell'])],
        [Paragraph("<b>4. Penyerahan di Unit</b>", styles['TableCellBold']), Paragraph("PIC Distribusi menyerahkan fisik barang kepada staf Subdirektorat di ruang kerja dan melakukan pencocokan fisik bersama.", styles['TableCell'])],
        [Paragraph("<b>5. Dokumentasi Foto</b>", styles['TableCellBold']), Paragraph("PIC Distribusi mengambil foto dokumentasi penyerahan fisik barang sebagai bukti autentik pertanggungjawaban BMN.", styles['TableCell'])],
        [Paragraph("<b>6. Cetak Bukti Resmi</b>", styles['TableCellBold']), Paragraph("Setelah status berubah menjadi <b>Selesai</b>, klik tombol <b>'Bukti'</b> untuk mencetak Lembar Tanda Terima berkop resmi Kementerian PKP RI.", styles['TableCell'])],
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
        "Kewajiban Pemeriksaan Fisik Barang Sebelum Validasi Selesai",
        "Periksa kondisi kelayakan fisik barang (tidak rusak, cacat, atau kurang jumlah) bersama PIC Distribusi sebelum mengonfirmasi serah terima. "
        "Lembar bukti serah terima yang telah diterbitkan berkekuatan hukum sebagai arsip audit BMN.",
        kind="warning",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 11: BAB IX - PANDUAN PEMECAHAN MASALAH (FAQ) & HELPDESK
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB IX: PANDUAN PEMECAHAN MASALAH (FAQ) & LAYANAN HELPDESK", styles))
    
    story.append(Paragraph("9.1 Tanya Jawab Operasional Subdirektorat (Frequently Asked Questions)", styles['SubChapterHeading']))

    faq_list = [
        (
            "T: Apakah akun Subdirektorat boleh mengajukan ATK untuk kebutuhan staf perorangan?",
            "J: Boleh, namun sangat dianjurkan untuk menghimpun kebutuhan seluruh staf dalam 1 (satu) pengajuan kolektif berkala "
            "(misal per minggu atau per bulan) agar proses administrasi dan penyerahan barang oleh PIC Gudang lebih efisien."
        ),
        (
            "T: Mengapa jumlah barang yang disetujui lebih sedikit dari yang diajukan di formulir?",
            "J: Kasubag TU berwenang melakukan kuota rasionalisasi berdasarkan ketersediaan stok fisik gudang "
            "dan azas pemerataan ke unit kerja lainnya. Rincian jumlah disetujui dapat dipantau pada tabel Riwayat Pengajuan."
        ),
        (
            "T: Apa yang harus dilakukan jika pengajuan tiket berstatus 'Ditolak' oleh Kasubag TU?",
            "J: Buka detail tiket dan baca alasan penolakan pada kolom 'Catatan Kasubag TU'. Perbaiki permohonan "
            "(misal menyesuaikan spesifikasi atau mengurangi kuantitas) kemudian ajukan tiket baru."
        ),
        (
            "T: Bagaimana jika barang yang diserahkan rusak atau tidak cocok saat serah terima fisik?",
            "J: Jangan menandatangani serah terima. Laporkan seketika kepada petugas PIC Distribusi di lokasi untuk "
            "dilakukan penukaran langsung dengan barang cadangan di gudang Subbag TU."
        ),
        (
            "T: Lupa kata sandi dan email dinas sedang offline, bagaimana cara masuk kembali?",
            "J: Gunakan menu 'Lupa Password' di laman login. Masukkan NIP 18 digit yang terdaftar dan ketikkan kata sandi baru. "
            "Sistem ATEKA tidak memerlukan verifikasi email SMTP, sehingga sandi baru langsung aktif seketika."
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
        [Paragraph("<b>Akses Layanan Portal</b>", styles['TableCellBold']), Paragraph(": Portal ATEKA Beroperasi 24 Jam Mandiri untuk Pengajuan Daring", styles['TableCell'])],
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
        "Komitmen Tata Kelola Logistik Berintegritas",
        "Mari bersama-sama mewujudkan pengelolaan persediaan dan pengadaan ATK yang transparan, akuntabel, "
        "dan tepat guna guna mendukung kelancaran program perumahan dan permukiman bagi seluruh rakyat Indonesia.",
        kind="info",
        styles=styles,
        width=505
    ))

    return story


# ==============================================================================
# 5. FUNGSI EKSEKUSI PEMBUATAN PDF
# ==============================================================================
def generate_pdf(output_filename="Manual_Book_Ateka_Subdirektorat.pdf"):
    print(f"[*] Menyiapkan pembuatan PDF: {output_filename}")
    
    base_dir = os.path.dirname(os.path.abspath(__file__))
    logo_path = os.path.join(base_dir, "public", "hope-ui", "assets", "images", "auth", "kemenpkp-logo.png")
    img_dir = os.path.join(base_dir, "assets", "manual_book_images")
    pdf_path = os.path.join(base_dir, output_filename)

    # Konfigurasi A4: 595.27 x 842 pt. Margin 45 pt kiri/kanan -> printable width 505.27 pt
    doc = SimpleDocTemplate(
        pdf_path,
        pagesize=A4,
        leftMargin=45,
        rightMargin=45,
        topMargin=46,
        bottomMargin=46,
        title="Buku Panduan Pengguna ATEKA - Role Subdirektorat",
        author="Subbagian Tata Usaha Kementerian PKP RI",
        subject="User Manual Book Role Subdirektorat (Pemohon Kolektif Unit Kerja)",
        creator="Sistem Inventori ATEKA Kementerian PKP RI"
    )

    styles = create_custom_styles()
    story = build_manual_book_story(styles, logo_path, img_dir)

    print(f"[*] Membangun dokumen dengan {len(story)} elemen Platypus...")
    doc.build(story, canvasmaker=AtekaNumberedCanvas)
    
    file_size_kb = os.path.getsize(pdf_path) / 1024
    print(f"[SUCCESS] PDF berhasil diterbitkan: {pdf_path} ({file_size_kb:.2f} KB)")
    return pdf_path


if __name__ == "__main__":
    out_file = sys.argv[1] if len(sys.argv) > 1 else "Manual_Book_Ateka_Subdirektorat.pdf"
    generate_pdf(out_file)
