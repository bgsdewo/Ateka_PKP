"""
Script Pembuat Buku Panduan Pengguna (User Manual Book)
Role PIC (Pengelola Gudang & PIC Distribusi) - Sistem ATEKA Kementerian PKP RI
Menggunakan ReportLab Platypus dengan Standar Desain Resmi & NumberedCanvas
Menyematkan seluruh 10 tangkapan layar antarmuka sistem secara proporsional.
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
            self.drawRightString(595 - 45, 842 - 30, "PANDUAN ATEKA • ROLE PIC DISTRIBUSI & GUDANG")
            
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
            self.drawString(45, 25, "Buku Panduan PIC (Pengelola Gudang & Distribusi) • Edisi Terintegrasi 2026")
            
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
        fontSize=12,
        leading=15,
        textColor=C_NAVY_DARK,
        spaceBefore=0,
        spaceAfter=2,
        keepWithNext=True,
    )
    styles['SubChapterHeading'] = ParagraphStyle(
        'SubChapterHeading',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=9,
        leading=12,
        textColor=C_NAVY_MID,
        spaceBefore=4,
        spaceAfter=2,
        keepWithNext=True,
    )
    styles['SubSubHeading'] = ParagraphStyle(
        'SubSubHeading',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8.2,
        leading=11,
        textColor=C_TEXT_MAIN,
        spaceBefore=3,
        spaceAfter=2,
        keepWithNext=True,
    )

    # Teks Paragraf Biasa
    styles['Body'] = ParagraphStyle(
        'Body',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.8,
        leading=10.8,
        textColor=C_TEXT_MAIN,
        spaceAfter=3,
    )
    styles['BodyJustify'] = ParagraphStyle(
        'BodyJustify',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.8,
        leading=10.8,
        textColor=C_TEXT_MAIN,
        alignment=4, # Justify
        spaceAfter=3,
    )
    styles['BulletText'] = ParagraphStyle(
        'BulletText',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.5,
        leading=10.2,
        textColor=C_TEXT_MAIN,
        spaceAfter=2,
    )
    styles['ImageCaption'] = ParagraphStyle(
        'ImageCaption',
        parent=base_styles['Normal'],
        fontName='Helvetica-Oblique',
        fontSize=7.0,
        leading=9.2,
        textColor=C_TEXT_MUTED,
        alignment=1, # Center
        spaceBefore=2,
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
        fontSize=7.6,
        leading=10.2,
        textColor=C_NAVY_DARK,
    )
    styles['StepDesc'] = ParagraphStyle(
        'StepDesc',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.2,
        leading=9.5,
        textColor=C_TEXT_MUTED,
    )

    # Tabel Styles
    styles['TableHeader'] = ParagraphStyle(
        'TableHeader',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=7.2,
        leading=9.2,
        textColor=colors.white,
        alignment=1,
    )
    styles['TableCell'] = ParagraphStyle(
        'TableCell',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.0,
        leading=9.2,
        textColor=C_TEXT_MAIN,
    )
    styles['TableCellBold'] = ParagraphStyle(
        'TableCellBold',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=7.0,
        leading=9.2,
        textColor=C_NAVY_DARK,
    )
    styles['TableCellCenter'] = ParagraphStyle(
        'TableCellCenter',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.0,
        leading=9.2,
        textColor=C_TEXT_MAIN,
        alignment=1,
    )

    # Callout Box Text
    styles['CalloutTitle'] = ParagraphStyle(
        'CalloutTitle',
        parent=base_styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=7.6,
        leading=9.8,
        textColor=C_NAVY_DARK,
        spaceAfter=1,
    )
    styles['CalloutText'] = ParagraphStyle(
        'CalloutText',
        parent=base_styles['Normal'],
        fontName='Helvetica',
        fontSize=7.0,
        leading=9.5,
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
        icon_label = "TIPS OPERASIONAL PIC GUDANG"
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
        ('TOPPADDING', (0,0), (-1,-1), 3.5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3.5),
        ('LEFTPADDING', (0,0), (-1,-1), 7),
        ('RIGHTPADDING', (0,0), (-1,-1), 7),
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
# 4. KONTEN DOKUMEN BUKU PANDUAN PENGGUNA (ROLE PIC DISTRIBUSI & GUDANG)
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
    story.append(Paragraph("ROLE PIC (PENGELOLA GUDANG & DISTRIBUSI)", styles['CoverRole']))

    story.append(Paragraph(
        "Panduan Standar Operasional Penyerahan Fisik Barang ATK ke Pemohon, "
        "Pencatatan Transaksi Masuk/Keluar, Pengelolaan Master Data Katalog, "
        "serta Rekapitulasi Laporan Mutasi & Berita Acara Resmi Persediaan Gudang.",
        styles['CoverAppDesc']
    ))

    meta_data = [
        [Paragraph("<b>Nama Aplikasi</b>", styles['TableCellBold']), Paragraph(": ATEKA (Aplikasi Tata Kelola Persediaan & Pengadaan ATK)", styles['TableCell'])],
        [Paragraph("<b>Sasaran Pengguna</b>", styles['TableCellBold']), Paragraph(": Petugas PIC Distribusi Unit & Pengelola Gudang Logistik Subbag TU", styles['TableCell'])],
        [Paragraph("<b>Klasifikasi Dokumen</b>", styles['TableCellBold']), Paragraph(": Panduan Operasional Kedinasan (Internal Kementerian PKP RI)", styles['TableCell'])],
        [Paragraph("<b>Versi Rilis Sistem</b>", styles['TableCellBold']), Paragraph(": Versi 2.0 (Edisi Khusus Pengelola Gudang & Distribusi - 2026)", styles['TableCell'])],
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
    # HALAMAN 2: PROFIL PERAN PIC & DAFTAR ISI
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("KATA PENGANTAR & PROFIL PERAN PIC DISTRIBUSI", styles))
    
    story.append(Paragraph(
        "<b>Role PIC (Person In Charge) Distribusi & Pengelola Gudang</b> memegang peranan vital sebagai ujung tombak "
        "operasional logistik di lingkungan Kementerian Perumahan dan Kawasan Permukiman RI. "
        "Petugas PIC bertindak sebagai jembatan antara persetujuan kebijakan Kasubag TU dengan penyerahan fisik nyata "
        "kepada para pemohon, sekaligus menjaga akurasi pembukuan inventori barang masuk, barang keluar, dan master data persediaan.",
        styles['BodyJustify']
    ))

    story.append(make_callout(
        "Ketentuan Khusus: Akun PIC Didaftarkan Langsung oleh Kasubag TU",
        "Berbeda dengan peran pemohon (Pegawai/Subdirektorat) yang dapat mendaftar mandiri, <b>Akun PIC dibuatkan dan "
        "ditetapkan secara terpusat oleh Kasubag TU</b> melalui menu Manajemen Pengguna. "
        "Setiap petugas PIC ditugaskan membina wilayah unit kerja tertentu (contoh: <i>PIC Distribusi Subdirektorat SDK</i>). "
        "Bila lupa kata sandi, petugas PIC dapat melakukan pemulihan instan mandiri menggunakan <b>NIP 18 Digit</b>.",
        kind="info",
        styles=styles,
        width=505
    ))

    story.append(Spacer(1, 4))
    story.append(Paragraph("Tugas Pokok & Wewenang Utama Role PIC", styles['SubChapterHeading']))
    tupoksi_data = [
        [Paragraph("<b>Bidang Tugas</b>", styles['TableHeader']), Paragraph("<b>Rincian Tanggung Jawab Operasional Sistem</b>", styles['TableHeader']), Paragraph("<b>Output Dokumen / Bukti</b>", styles['TableHeader'])],
        [Paragraph("<b>Distribusi Barang</b>", styles['TableCellBold']), Paragraph("Menyiapkan fisik barang sesuai tiket disetujui TU, menyerahkan ke pemohon, dan mengunggah foto serah terima.", styles['TableCell']), Paragraph("Bukti Tanda Terima Serah Terima", styles['TableCellCenter'])],
        [Paragraph("<b>Mutasi Barang Masuk</b>", styles['TableCellBold']), Paragraph("Mencatat faktur belanja, supplier, harga satuan, kondisi fisik, dan kuantitas barang yang baru masuk ke gudang.", styles['TableCell']), Paragraph("Buku Transaksi Barang Masuk", styles['TableCellCenter'])],
        [Paragraph("<b>Mutasi Barang Keluar</b>", styles['TableCellBold']), Paragraph("Mencatat pengeluaran stok fisik dengan format catatan: <i>'Pengeluaran Barang Apa Untuk Siapa'</i>.", styles['TableCell']), Paragraph("Buku Transaksi Barang Keluar", styles['TableCellCenter'])],
        [Paragraph("<b>Master Data Katalog</b>", styles['TableCellBold']), Paragraph("Menambah dan memperbarui data kategori barang serta master produk (kode, satuan, batas minimum).", styles['TableCell']), Paragraph("Master Data Katalog Persediaan", styles['TableCellCenter'])],
        [Paragraph("<b>Rekapitulasi & Arsip</b>", styles['TableCellBold']), Paragraph("Memantau valuasi persediaan (Rp), saldo fisik, serta mengekspor laporan bulanan ke Excel, PDF, & Berita Acara.", styles['TableCell']), Paragraph("Berita Acara Rekapitulasi Fisik", styles['TableCellCenter'])],
    ]
    tupoksi_table = Table(tupoksi_data, colWidths=[95, 275, 135])
    tupoksi_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    for r in range(1, len(tupoksi_data)):
        if r % 2 == 0:
            tupoksi_table.setStyle(TableStyle([('BACKGROUND', (0, r), (-1, r), C_BG_LIGHT)]))
    story.append(tupoksi_table)

    story.append(Spacer(1, 6))
    story.extend(make_header_bar("DAFTAR ISI PANDUAN PENGGUNA", styles))

    toc_data = [
        [Paragraph("<b>Bab</b>", styles['TableHeader']), Paragraph("<b>Pokok Pembahasan & Modul Sistem ATEKA</b>", styles['TableHeader']), Paragraph("<b>Tangkapan Layar</b>", styles['TableHeader']), Paragraph("<b>Halaman</b>", styles['TableHeader'])],
        [Paragraph("<b>Bab I</b>", styles['TableCellBold']), Paragraph("<b>Autentikasi Akun PIC & Prosedur Login Sistem</b>", styles['TableCell']), Paragraph("image1.png (Mobile Login)", styles['TableCellCenter']), Paragraph("Hal 3", styles['TableCellCenter'])],
        [Paragraph("<b>Bab II</b>", styles['TableCellBold']), Paragraph("<b>Eksplorasi Dashboard PIC & 3 Indikator Inventori</b>", styles['TableCell']), Paragraph("image2.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 4", styles['TableCellCenter'])],
        [Paragraph("<b>Bab III</b>", styles['TableCellBold']), Paragraph("<b>Koordinasi & Distribusi Penyerahan Barang Fisik</b>", styles['TableCell']), Paragraph("image3.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 5", styles['TableCellCenter'])],
        [Paragraph("<b>Bab IV</b>", styles['TableCellBold']), Paragraph("<b>Pengelolaan Gudang — Pencatatan Barang Masuk</b>", styles['TableCell']), Paragraph("image4.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 6", styles['TableCellCenter'])],
        [Paragraph("<b>Bab V</b>", styles['TableCellBold']), Paragraph("<b>Pengelolaan Gudang — Pencatatan Barang Keluar</b>", styles['TableCell']), Paragraph("image5.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 7", styles['TableCellCenter'])],
        [Paragraph("<b>Bab VI</b>", styles['TableCellBold']), Paragraph("<b>Laporan Mutasi Persediaan & Ekspor Dokumen</b>", styles['TableCell']), Paragraph("image6.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 8", styles['TableCellCenter'])],
        [Paragraph("<b>Bab VII</b>", styles['TableCellBold']), Paragraph("<b>Rekapitulasi Arsip & Cetak Berita Acara Resmi</b>", styles['TableCell']), Paragraph("image7.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 9", styles['TableCellCenter'])],
        [Paragraph("<b>Bab VIII</b>", styles['TableCellBold']), Paragraph("<b>Master Data — Pengelolaan Kategori Barang ATK</b>", styles['TableCell']), Paragraph("image8.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 10", styles['TableCellCenter'])],
        [Paragraph("<b>Bab IX</b>", styles['TableCellBold']), Paragraph("<b>Master Data — Pengelolaan Katalog Daftar Barang</b>", styles['TableCell']), Paragraph("image9.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 11", styles['TableCellCenter'])],
        [Paragraph("<b>Bab X</b>", styles['TableCellBold']), Paragraph("<b>Pusat Notifikasi Navbar & Alert Distribusi Tiket</b>", styles['TableCell']), Paragraph("image10.png (MacBook)", styles['TableCellCenter']), Paragraph("Hal 12", styles['TableCellCenter'])],
        [Paragraph("<b>Bab XI</b>", styles['TableCellBold']), Paragraph("<b>SOP Distribusi Fisik BMN & Tanya Jawab (FAQ)</b>", styles['TableCell']), Paragraph("SOP & Meja Bantuan", styles['TableCellCenter']), Paragraph("Hal 13", styles['TableCellCenter'])],
    ]
    toc_table = Table(toc_data, colWidths=[40, 265, 150, 50])
    toc_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_DARK),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 2.2),
        ('BOTTOMPADDING', (0,0), (-1,-1), 2.2),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    for r in range(1, len(toc_data)):
        if r % 2 == 0:
            toc_table.setStyle(TableStyle([('BACKGROUND', (0, r), (-1, r), C_BG_LIGHT)]))
    story.append(toc_table)

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 3: BAB I - AUTENTIKASI AKUN PIC & LOGIN (image1.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB I: AUTENTIKASI AKUN PIC & PROSEDUR LOGIN SISTEM", styles))
    
    story.append(Paragraph(
        "Petugas PIC Distribusi dan Pengelola Gudang mendapatkan akun resmi yang telah dibuatkan secara khusus "
        "oleh Kepala Subbagian Tata Usaha (Kasubag TU). Login dapat dilakukan melalui perangkat komputer maupun ponsel.",
        styles['BodyJustify']
    ))

    img1_path = os.path.join(img_dir, "image1.png")
    # Asli: 457 x 683 (rasio 0.669) -> lebar 180, tinggi 269
    img1_box = make_screenshot_box(img1_path, 180, 269, "Form Login Akun PIC (fairuz@pkp.com)", styles)

    auth_steps = [
        make_step_row(1, "Terima Kredensial dari Kasubag TU", "Kasubag TU mendaftarkan akun PIC melalui menu Manajemen Pengguna dan memberikan alamat email dinas serta kata sandi awal.", styles),
        make_step_row(2, "Akses Halaman Login (/login)", "Buka browser Google Chrome atau Microsoft Edge, buka portal ATEKA pada alamat server yang ditentukan.", styles),
        make_step_row(3, "Masukkan Email Kedinasan PIC", "Ketikkan alamat email yang didaftarkan (contoh pada screenshot di samping: <b>fairuz@pkp.com</b>).", styles),
        make_step_row(4, "Masukkan Kata Sandi", "Ketik kata sandi akun Anda. Anda dapat mengklik ikon mata untuk memastikan karakter sandi telah benar.", styles),
        make_step_row(5, "Klik Tombol 'Masuk ke Sistem'", "Sistem memvalidasi role PIC dan langsung mengarahkan Anda ke <b>Dashboard PIC Distribusi / Pengelola Gudang</b>.", styles),
        make_step_row(6, "Mekanisme Lupa Password via NIP", "Bila Anda lupa kata sandi, klik <b>'Lupa Password?'</b>, masukkan NIP 18 digit resmi Anda, dan buat kata sandi baru secara instan tanpa perlu link email.", styles),
    ]

    auth_table = Table([[img1_box, auth_steps]], colWidths=[195, 310])
    auth_table.setStyle(TableStyle([
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('LEFTPADDING', (0,0), (-1,-1), 0),
        ('RIGHTPADDING', (0,0), (-1,-1), 0),
        ('TOPPADDING', (0,0), (-1,-1), 0),
        ('BOTTOMPADDING', (0,0), (-1,-1), 0),
    ]))
    story.append(auth_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Hak Akses Khusus Petugas PIC Distribusi",
        "Sebagai pengelola gudang, akun PIC memiliki wewenang mengelola transaksi barang masuk, mencatat mutasi pengeluaran, "
        "memvalidasi serah terima barang kepada pemohon, mengelola master data barang, serta mencetak laporan rekapitulasi inventori.",
        kind="warning",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 4: BAB II - DASHBOARD PIC (image2.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB II: EKSPLORASI DASHBOARD PIC & 3 INDIKATOR INVENTORI", styles))
    
    story.append(Paragraph(
        "Dashboard PIC Distribusi dirancang untuk memantau performa persediaan gudang secara menyeluruh. "
        "Halaman ini menyajikan status tugas serah terima, tombol tindakan cepat, filter periode data, serta nilai valuasi aset ATK.",
        styles['BodyJustify']
    ))

    img2_path = os.path.join(img_dir, "image2.png")
    # Asli 1184 x 743 (rasio 1.594) -> lebar 425, tinggi 266
    img2_box = make_screenshot_box(img2_path, 425, 266, "Dashboard Inventori - Fairuz Armina Nur Azizah (PIC Distribusi SDK)", styles)
    story.append(img2_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Komponen Antarmuka Utama & Penjelasan Indikator Inventori", styles['SubChapterHeading']))

    dash_guide_data = [
        [Paragraph("<b>Elemen Dashboard</b>", styles['TableHeader']), Paragraph("<b>Tampilan pada Screenshot (Gambar di Atas) & Fungsinya</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Header Identitas</b>", styles['TableCellBold']), Paragraph("Menampilkan nama lengkap: <i>Fairuz Armina Nur Azizah</i>, unit penugasan <i>PIC Distribusi Subdirektorat SDK</i>, dan badge kuning <i>'PIC Distribusi'</i>.", styles['TableCell'])],
        [Paragraph("<b>2. Tiga Tombol Aksi Cepat</b>", styles['TableCellBold']), Paragraph("<b>Distribusi ATK (Siap Serah):</b> Tombol oranye menuju tiket yang siap diserahkan.<br/>"
                                                                                 "<b>Catat Pengeluaran Gudang:</b> Akses instan pencatatan mutasi barang keluar.<br/>"
                                                                                 "<b>Cek Stok Fisik:</b> Pintasan memeriksa sisa persediaan riil gudang.", styles['TableCell'])],
        [Paragraph("<b>3. Filter Periode Data</b>", styles['TableCellBold']), Paragraph("Memilih Bulan (Oktober) dan Tahun (2026) untuk menyaring data monitoring logistik berjalan.", styles['TableCell'])],
        [Paragraph("<b>4. Total Item Master</b>", styles['TableCellBold']), Paragraph("Menampilkan jumlah jenis/kategori barang ATK yang terdaftar aktif dalam katalog master sistem.", styles['TableCell'])],
        [Paragraph("<b>5. Total Stok Fisik</b>", styles['TableCellBold']), Paragraph("Akumulasi seluruh unit fisik barang (Pcs, Rim, Dos, dll.) yang tersimpan nyata di ruang gudang.", styles['TableCell'])],
        [Paragraph("<b>6. Valuasi Persediaan (Rp)</b>", styles['TableCellBold']), Paragraph("Menghitung total nilai nominal rupiah seluruh sisa persediaan fisik di gudang (Saldo = Masuk &ndash; Keluar).", styles['TableCell'])],
    ]
    dash_guide_table = Table(dash_guide_data, colWidths=[120, 385])
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
        "Koneksi Valuasi Persediaan Real-Time",
        "Angka pada kartu 'Valuasi Persediaan' dihitung otomatis berdasarkan perkalian harga satuan faktur masuk dengan sisa stok fisik. "
        "Data ini menjadi acuan penting untuk pelaporan aset Barang Milik Negara (BMN) Subbag Tata Usaha.",
        kind="info",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 5: BAB III - DISTRIBUSI BARANG (image3.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB III: KOORDINASI & DISTRIBUSI PENYERAHAN BARANG FISIK", styles))
    
    story.append(Paragraph(
        "Menu Distribusi Barang merupakan pusat kerja harian PIC Distribusi untuk memproses penyerahan fisik ATK kepada pemohon. "
        "Di sini terkumpul seluruh permohonan yang telah disetujui Kasubag TU, baik dari unit Subdirektorat maupun staf Pegawai.",
        styles['BodyJustify']
    ))

    img3_path = os.path.join(img_dir, "image3.png")
    # Asli 1243 x 788 (rasio 1.577) -> lebar 425, tinggi 269
    img3_box = make_screenshot_box(img3_path, 425, 269, "Koordinasi & Distribusi Barang ATK - Daftar Penyerahan ke Pemohon", styles)
    story.append(img3_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Empat Kartu Metrik Distribusi & Tata Cara Penyerahan Barang Fisik", styles['SubChapterHeading']))

    dist_guide_data = [
        [Paragraph("<b>Komponen Halaman</b>", styles['TableHeader']), Paragraph("<b>Penjelasan Fungsional & Tindakan PIC Distribusi</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Siap Didistribusikan</b>", styles['TableCellBold']), Paragraph("Menampilkan jumlah tiket yang telah disetujui Kasubag TU dan menunggu penyerahan fisik oleh PIC.", styles['TableCell'])],
        [Paragraph("<b>2. Permintaan Subdit</b>", styles['TableCellBold']), Paragraph("Menghitung paket permohonan berskala kolektif dari 4 Subdirektorat resmi yang menjadi binaan Anda.", styles['TableCell'])],
        [Paragraph("<b>3. Permintaan Pegawai</b>", styles['TableCellBold']), Paragraph("Menghitung paket permohonan personal staf perorangan yang harus diserahkan ke meja kerja.", styles['TableCell'])],
        [Paragraph("<b>4. Selesai Diserahkan</b>", styles['TableCellBold']), Paragraph("Menampilkan total transaksi yang telah sukses diserahterimakan dan dilengkapi dokumentasi foto.", styles['TableCell'])],
        [Paragraph("<b>5. Filter & Pencarian</b>", styles['TableCellBold']), Paragraph("Filter berdasarkan Status (Semua/Pending/Selesai), Tipe Pemohon, Periode Bulan, Tahun, dan Nomor Tiket.", styles['TableCell'])],
        [Paragraph("<b>6. Tombol Serah Terima</b>", styles['TableCellBold']), Paragraph("Klik tombol <b>'Serahkan'</b> pada baris tiket: periksa kecocokan fisik barang, unggah foto bukti penyerahan, dan simpan.", styles['TableCell'])],
    ]
    dist_guide_table = Table(dist_guide_data, colWidths=[120, 385])
    dist_guide_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(dist_guide_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Kewajiban Pengambilan Foto Dokumentasi Serah Terima",
        "Setiap penyerahan fisik wajib difoto bersama perwakilan pemohon di lokasi unit kerja. "
        "Foto ini akan disematkan secara otomatis ke dalam dokumen Berita Acara Bukti Serah Terima resmi berkop Kementerian PKP RI.",
        kind="warning",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 6: BAB IV - TRANSAKSI BARANG MASUK (image4.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB IV: PENGELOLAAN GUDANG &bull; PENCATATAN BARANG MASUK", styles))
    
    story.append(Paragraph(
        "Menu Transaksi Barang Masuk digunakan oleh petugas gudang untuk mencatat setiap penambahan persediaan ATK yang diperoleh "
        "dari hasil pengadaan baru atau belanja rekanan/supplier resmi berdasarkan dokumen faktur pembelian sah.",
        styles['BodyJustify']
    ))

    img4_path = os.path.join(img_dir, "image4.png")
    # Asli 1135 x 751 (rasio 1.511) -> lebar 415, tinggi 274
    img4_box = make_screenshot_box(img4_path, 415, 274, "Transaksi Barang Masuk - Logistik & Penerimaan Stok Berbasis Faktur", styles)
    story.append(img4_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Struktur Kolom Tabel Barang Masuk & Aturan Pencatatan Standar", styles['SubChapterHeading']))

    masuk_guide_data = [
        [Paragraph("<b>Kolom / Fitur</b>", styles['TableHeader']), Paragraph("<b>Ketentuan Pengisian Data & Standar Operasional Gudang</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Filter Rentang Waktu</b>", styles['TableCellBold']), Paragraph("Menyaring riwayat faktur barang masuk berdasarkan Periode Bulan, Tahun, atau rentang tanggal spesifik (Dari &ndash; Sampai Tanggal).", styles['TableCell'])],
        [Paragraph("<b>2. No. Faktur & Tanggal</b>", styles['TableCellBold']), Paragraph("Wajib mencatat nomor faktur resmi dari penyedia/rekanan beserta tanggal faktur pembelian yang tercantum pada kuitansi belanja.", styles['TableCell'])],
        [Paragraph("<b>3. Nama Barang & Kondisi</b>", styles['TableCellBold']), Paragraph("Memilih nama barang dari master data dan memeriksa kondisi fisik penerimaan (Normal / Baik).", styles['TableCell'])],
        [Paragraph("<b>4. Uraian / Catatan Masuk</b>", styles['TableCellBold']), Paragraph("<b>Standar Format Wajib:</b> Kalimat uraian barang masuk diformat resmi: <i>'Pembelian Barang Apa'</i> (misal: <i>Pembelian Kertas A4</i>).", styles['TableCell'])],
        [Paragraph("<b>5. Harga Satuan & Jumlah</b>", styles['TableCellBold']), Paragraph("Ketikkan harga satuan perolehan (Rp) dan jumlah volume unit masuk. Sistem otomatis menambah stok fisik di master inventori.", styles['TableCell'])],
    ]
    masuk_guide_table = Table(masuk_guide_data, colWidths=[120, 385])
    masuk_guide_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(masuk_guide_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Sinkronisasi Otomatis Stok Fisik & Realtime Mutasi",
        "Setiap kali transaksi barang masuk disimpan, sistem seketika meningkatkan jumlah sisa stok fisik di E-Katalog "
        "dan otomatis memperbarui saldo perolehan di buku laporan persediaan tanpa perlu sinkronisasi manual.",
        kind="success",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 7: BAB V - TRANSAKSI BARANG KELUAR (image5.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB V: PENGELOLAAN GUDANG &bull; PENCATATAN BARANG KELUAR", styles))
    
    story.append(Paragraph(
        "Menu Transaksi Barang Keluar mencatat seluruh mutasi pengurangan stok fisik ATK dari ruang persediaan gudang. "
        "Setiap pengeluaran wajib mengacu pada tiket permintaan pemohon yang telah disetujui Kasubag TU.",
        styles['BodyJustify']
    ))

    img5_path = os.path.join(img_dir, "image5.png")
    # Asli 1239 x 761 (rasio 1.628) -> lebar 425, tinggi 261
    img5_box = make_screenshot_box(img5_path, 425, 261, "Transaksi Barang Keluar - Mutasi Pengeluaran & Distribusi Gudang", styles)
    story.append(img5_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Struktur Kolom Tabel Barang Keluar & Standar Penulisan Catatan", styles['SubChapterHeading']))

    keluar_guide_data = [
        [Paragraph("<b>Kolom / Fitur</b>", styles['TableHeader']), Paragraph("<b>Ketentuan Informasi & Format Catatan Pengeluaran Resmi</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Tanggal Pengeluaran</b>", styles['TableCellBold']), Paragraph("Tanggal dan waktu riil barang fisik dikeluarkan dari rak penyimpanan gudang dan diserahkan ke pemohon.", styles['TableCell'])],
        [Paragraph("<b>2. Nama Barang & Volume</b>", styles['TableCellBold']), Paragraph("Item barang yang dikeluarkan beserta kuantitas unitnya (Pcs, Rim, Dos, Pak).", styles['TableCell'])],
        [Paragraph("<b>3. Format Catatan Keluar</b>", styles['TableCellBold']), Paragraph("<b>Standar Format Wajib:</b> Kalimat uraian diformat tegas: <i>'Pengeluaran Barang Apa Untuk Siapa'</i> (contoh: <i>Pengeluaran Kertas A4 Untuk Subdirektorat SDK</i>).", styles['TableCell'])],
        [Paragraph("<b>4. Kondisi & Sisa Stok</b>", styles['TableCellBold']), Paragraph("Mencatat kondisi fisik saat diserahkan serta menampilkan sisa stok fisik gudang setelah dikurangi mutasi tersebut.", styles['TableCell'])],
        [Paragraph("<b>5. Status Distribusi</b>", styles['TableCellBold']), Paragraph("Menunjukkan status penyerahan: <i>Diserahkan</i> setelah PIC mengunggah bukti foto dan dikonfirmasi pemohon.", styles['TableCell'])],
    ]
    keluar_guide_table = Table(keluar_guide_data, colWidths=[120, 385])
    keluar_guide_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(keluar_guide_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Kepatuhan Standar Deskripsi Audit Mutasi",
        "Keseragaman penulisan 'Pengeluaran [Nama Barang] Untuk [Nama Unit/Pegawai]' sangat penting agar laporan buku pengeluaran "
        "dapat diaudit dengan cepat oleh inspektorat tanpa multitafsir peruntukan barang.",
        kind="tip",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 8: BAB VI - LAPORAN & ARSIP: MUTASI PERSEDIAAN (image6.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB VI: LAPORAN MUTASI PERSEDIAAN & EKSPOR DOKUMEN", styles))
    
    story.append(Paragraph(
        "Menu Laporan & Arsip menyajikan rekapitulasi mutasi logistik barang masuk dan barang keluar secara komprehensif. "
        "Petugas PIC dapat menyaring periode data, memantau total kuantitas, serta mengekspor data ke berbagai format resmi.",
        styles['BodyJustify']
    ))

    img6_path = os.path.join(img_dir, "image6.png")
    # Asli 1442 x 817 (rasio 1.765) -> lebar 435, tinggi 246
    img6_box = make_screenshot_box(img6_path, 435, 246, "Laporan Mutasi Persediaan & Fitur Ekspor Excel, PDF, Cetak Laporan", styles)
    story.append(img6_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Tiga Kartu Metrik Transaksi & Pilihan Ekspor Dokumen Resmi", styles['SubChapterHeading']))

    lap_guide_data = [
        [Paragraph("<b>Fitur & Tombol</b>", styles['TableHeader']), Paragraph("<b>Fungsi & Informasi yang Dihasilkan pada Sistem</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Tiga Kartu Metrik</b>", styles['TableCellBold']), Paragraph("<b>Total Transaksi:</b> Akumulasi seluruh mutasi tercatat.<br/>"
                                                                                 "<b>Total Barang Masuk (+Pcs):</b> Jumlah unit ATK yang telah diterima gudang.<br/>"
                                                                                 "<b>Total Barang Keluar (&minus;Pcs):</b> Jumlah unit ATK yang telah diserahkan.", styles['TableCell'])],
        [Paragraph("<b>2. Filter Multi-Kriteria</b>", styles['TableCellBold']), Paragraph("Menyaring berdasarkan Periode Bulan, Tahun, Jenis Mutasi (Semua / Masuk Saja / Keluar Saja), Kategori Pemohon, dan Rentang Tanggal Spesifik.", styles['TableCell'])],
        [Paragraph("<b>3. Tombol 'Export Excel'</b>", styles['TableCellBold']), Paragraph("Mengunduh file spreadsheet <b>.xlsx</b> lengkap dengan header kementerian, formula penjumlahan otomatis, dan format tabel dinas.", styles['TableCell'])],
        [Paragraph("<b>4. Tombol 'Export PDF'</b>", styles['TableCellBold']), Paragraph("Menerbitkan file PDF beresolusi tinggi berkop Kementerian PKP RI, siap arsip atau dibagikan ke pimpinan.", styles['TableCell'])],
        [Paragraph("<b>5. Tombol 'Cetak Laporan'</b>", styles['TableCellBold']), Paragraph("Membuka tampilan cetak langsung (Print View) dengan tanggal hari berbahasa Indonesia dan kolom tanda tangan pengelola gudang.", styles['TableCell'])],
    ]
    lap_guide_table = Table(lap_guide_data, colWidths=[120, 385])
    lap_guide_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(lap_guide_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Validitas Laporan Bertanda Tangan Kedinasan",
        "Setiap dokumen ekspor PDF dan cetak print view dilengkapi tempat tanda tangan resmi Pengelola Inventori dan Kasubag TU "
        "sebagai dokumen pertanggungjawaban pengelolaan logistik BMN berkala.",
        kind="info",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 9: BAB VII - REKAPITULASI ARSIP & BERITA ACARA (image7.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB VII: REKAPITULASI ARSIP & CETAK BERITA ACARA RESMI", styles))
    
    story.append(Paragraph(
        "Sub-modul Rekapitulasi Arsip Dokumen menyajikan ikhtisar saldo akhir persediaan ATK dan valuasi nilai aset barang secara menyeluruh. "
        "Di halaman ini, PIC Distribusi dapat menerbitkan Berita Acara Rekapitulasi Resmi berkop kementerian.",
        styles['BodyJustify']
    ))

    img7_path = os.path.join(img_dir, "image7.png")
    # Asli 1282 x 784 (rasio 1.635) -> lebar 425, tinggi 260
    img7_box = make_screenshot_box(img7_path, 425, 260, "Rekapitulasi Mutasi & Arsip Dokumen - Valuasi Saldo & Berita Acara", styles)
    story.append(img7_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Parameter Penyaringan & Penerbitan Berita Acara Rekapitulasi", styles['SubChapterHeading']))

    rekap_guide_data = [
        [Paragraph("<b>Fitur & Metrik</b>", styles['TableHeader']), Paragraph("<b>Penjelasan Nilai Saldo & Instrumen Dokumen Resmi</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Total Item Terdaftar</b>", styles['TableCellBold']), Paragraph("Menghitung ragam item barang aktif yang terbagi dalam seluruh kategori ATK resmi Kementerian PKP RI.", styles['TableCell'])],
        [Paragraph("<b>2. Total Saldo Fisik Stok</b>", styles['TableCellBold']), Paragraph("Jumlah bersih akumulasi fisik persediaan yang masih tersisa di gudang dan siap didistribusikan.", styles['TableCell'])],
        [Paragraph("<b>3. Nilai Aset Saldo (Rp)</b>", styles['TableCellBold']), Paragraph("Valuasi perolehan aset persediaan (Rupiah) berdasarkan perhitungan saldo barang masuk dikurangi pengeluaran.", styles['TableCell'])],
        [Paragraph("<b>4. Filter Rekapitulasi</b>", styles['TableCellBold']), Paragraph("Parameter penyaringan menurut Periode Bulan, Tahun Kalender Anggaran (2026), Jenis Mutasi, serta Kelompok Pemohon.", styles['TableCell'])],
        [Paragraph("<b>5. Cetak Berita Acara</b>", styles['TableCellBold']), Paragraph("Tombol merah <b>'Cetak Berita Acara Rekapitulasi Resmi'</b> menerbitkan dokumen formal berformat kedinasan dengan Kop Kementerian PKP RI, matriks per kategori, dan lembar legalitas.", styles['TableCell'])],
    ]
    rekap_guide_table = Table(rekap_guide_data, colWidths=[120, 385])
    rekap_guide_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(rekap_guide_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Fungsi Yuridis Berita Acara Rekapitulasi",
        "Berita Acara Rekapitulasi Persediaan yang diterbitkan dari halaman ini berfungsi sebagai bukti fisik resmi stock opname "
        "bulanan dan semesteran yang diserahkan kepada pimpinan dan pemeriksa BMN.",
        kind="warning",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 10: BAB VIII - MASTER DATA: KATEGORI BARANG (image8.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB VIII: MASTER DATA &bull; PENGELOLAAN KATEGORI BARANG ATK", styles))
    
    story.append(Paragraph(
        "Kategori Barang merupakan struktur hierarki dasar untuk mengelompokkan ragam alat tulis kantor (Amplop, Balliner, Ballpoint, "
        "Buku Tulis, Kertas, Pensil, Spidol, dll.). Pengelolaan kategori yang tertib memudahkan pemohon dalam mencari barang di E-Katalog.",
        styles['BodyJustify']
    ))

    img8_path = os.path.join(img_dir, "image8.png")
    # Asli 1342 x 771 (rasio 1.741) -> lebar 430, tinggi 247
    img8_box = make_screenshot_box(img8_path, 430, 247, "Master Data Kategori Barang ATK - Klasifikasi & Pengelompokan Produk", styles)
    story.append(img8_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Pengoperasian Master Kategori Barang ATK", styles['SubChapterHeading']))

    kat_guide_data = [
        [Paragraph("<b>Operasi / Fitur</b>", styles['TableHeader']), Paragraph("<b>Langkah Teknis & Ketentuan Pengelolaan Kategori Barang</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Tambah Kategori Baru</b>", styles['TableCellBold']), Paragraph("Klik tombol biru <b>'Tambah Kategori'</b> &rarr; isi Nama Kategori (misal: <i>Map Odner</i>) dan Deskripsi singkat &rarr; klik Simpan.", styles['TableCell'])],
        [Paragraph("<b>2. Tabel Daftar Kategori</b>", styles['TableCellBold']), Paragraph("Menampilkan Nomor, Nama Kategori, Deskripsi, Tanggal Dibuat, dan tombol aksi pemeliharaan data.", styles['TableCell'])],
        [Paragraph("<b>3. Tombol 'Detail'</b>", styles['TableCellBold']), Paragraph("Melihat daftar seluruh produk atau barang ATK yang tergabung di bawah naungan kategori tersebut.", styles['TableCell'])],
        [Paragraph("<b>4. Tombol 'Edit'</b>", styles['TableCellBold']), Paragraph("Memperbaiki nama atau deskripsi kategori bila terjadi kesalahan penulisan nama kelompok barang.", styles['TableCell'])],
        [Paragraph("<b>5. Tombol 'Hapus'</b>", styles['TableCellBold']), Paragraph("Menghapus kategori. Sistem memiliki proteksi: kategori yang masih memiliki produk aktif tidak dapat dihapus sembarangan.", styles['TableCell'])],
    ]
    kat_guide_table = Table(kat_guide_data, colWidths=[120, 385])
    kat_guide_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(kat_guide_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Standarisasi Nomenklatur Kategori",
        "Gunakan penamaan kategori yang baku dan umum di instansi pemerintah agar staf pemohon dari seluruh subdirektorat "
        "dapat dengan mudah menemukan perlengkapan kerja yang dibutuhkannya.",
        kind="tip",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 11: BAB IX - MASTER DATA: DAFTAR BARANG ATK (image9.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB IX: MASTER DATA &bull; PENGELOLAAN KATALOG DAFTAR BARANG", styles))
    
    story.append(Paragraph(
        "Master Data Barang menyimpan database lengkap seluruh item persediaan ATK kantor, mencakup kode barang resmi, kategori, "
        "satuan standar, batas stok minimum, riwayat faktur perolehan, serta status ketersediaan di gudang.",
        styles['BodyJustify']
    ))

    img9_path = os.path.join(img_dir, "image9.png")
    # Asli 1299 x 831 (rasio 1.563) -> lebar 420, tinggi 268
    img9_box = make_screenshot_box(img9_path, 420, 268, "Master Data Barang - Pengelolaan Kode, Satuan, Stok & Faktur", styles)
    story.append(img9_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Pengoperasian Master Data Produk & Parameter Barang", styles['SubChapterHeading']))

    brg_guide_data = [
        [Paragraph("<b>Fitur & Field</b>", styles['TableHeader']), Paragraph("<b>Penjelasan Teknis & Ketentuan Master Data Persediaan</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Tambah Barang Baru</b>", styles['TableCellBold']), Paragraph("Klik tombol <b>'Tambah Barang'</b> untuk mendaftarkan item ATK baru yang akan menjadi bagian dari katalog resmi gudang.", styles['TableCell'])],
        [Paragraph("<b>2. Kode Barang Resmi</b>", styles['TableCellBold']), Paragraph("Kodefikasi unik (contoh: <i>ATK-001, ATK-004, BRG-002</i>) yang menjadi identitas barang pada barcode dan pencatatan BMN.", styles['TableCell'])],
        [Paragraph("<b>3. Kategori & Satuan</b>", styles['TableCellBold']), Paragraph("Menentukan kategori induk dan satuan standar (Pcs, Rim, Dos/Dus, Pak, Box, Lusin, Lembar, Buku, Set, Roll).", styles['TableCell'])],
        [Paragraph("<b>4. Stok Minimum</b>", styles['TableCellBold']), Paragraph("Menentukan ambang batas minimum persediaan. Jika stok mencapai angka ini, meter katalog otomatis berubah menjadi kuning/oranye.", styles['TableCell'])],
        [Paragraph("<b>5. Faktur & Supplier</b>", styles['TableCellBold']), Paragraph("Merekam nomor faktur dan nama rekanan penyedia saat barang pertama kali didaftarkan ke sistem persediaan.", styles['TableCell'])],
    ]
    brg_guide_table = Table(brg_guide_data, colWidths=[120, 385])
    brg_guide_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(brg_guide_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Keterkaitan Master Barang dengan E-Katalog Pemohon",
        "Setiap item yang didaftarkan pada Master Data Barang akan langsung tampil secara visual di E-Katalog Subdirektorat dan Pegawai. "
        "Pastikan foto barang dan deskripsi teknis telah lengkap dan informatif.",
        kind="info",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 12: BAB X - PUSAT NOTIFIKASI & SERAH TERIMA FISIK (image10.png)
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB X: PUSAT NOTIFIKASI NAVBAR & ALERT DISTRIBUSI TIKET", styles))
    
    story.append(Paragraph(
        "Sistem ATEKA dilengkapi fitur notifikasi lonceng cerdas di bilah navigasi atas (Navbar). "
        "Fitur ini memberikan pemberitahuan real-time kepada petugas PIC setiap kali ada tiket baru yang disetujui Kasubag TU.",
        styles['BodyJustify']
    ))

    img10_path = os.path.join(img_dir, "image10.png")
    # Asli 1326 x 750 (rasio 1.768) -> lebar 435, tinggi 246
    img10_box = make_screenshot_box(img10_path, 435, 246, "Fitur Notifikasi Navbar - Pemberitahuan Real-Time Tiket Siap Distribusi", styles)
    story.append(img10_box)

    story.append(Spacer(1, 4))
    story.append(Paragraph("Mekanisme Notifikasi & Alur Eksekusi Serah Terima Fisik", styles['SubChapterHeading']))

    notif_guide_data = [
        [Paragraph("<b>Tahapan</b>", styles['TableHeader']), Paragraph("<b>Uraian Tindakan PIC Distribusi (Sesuai Screenshot di Atas)</b>", styles['TableHeader'])],
        [Paragraph("<b>1. Lonceng Navbar</b>", styles['TableCellBold']), Paragraph("Ikon lonceng menampilkan badge angka merah saat ada tiket permohonan baru yang telah disetujui oleh Kasubag TU.", styles['TableCell'])],
        [Paragraph("<b>2. Popover Notifikasi</b>", styles['TableCellBold']), Paragraph("Mengklik ikon lonceng membuka dropdown popover yang menampilkan status pesan terkini (contoh: <i>0 belum dibaca</i>).", styles['TableCell'])],
        [Paragraph("<b>3. Tombol 'Menu Distribusi'</b>", styles['TableCellBold']), Paragraph("Klik tautan biru <b>'Menu Distribusi &rarr;'</b> pada bagian bawah jendela popover untuk langsung melompat ke tabel penyerahan barang.", styles['TableCell'])],
        [Paragraph("<b>4. Penyiapan Barang Gudang</b>", styles['TableCellBold']), Paragraph("Berdasarkan notifikasi, PIC mengambil fisik barang dari rak persediaan gudang sesuai jumlah kuota yang disetujui.", styles['TableCell'])],
        [Paragraph("<b>5. Serah Terima di Lokasi</b>", styles['TableCellBold']), Paragraph("PIC mendatangi ruang kerja pemohon, mencocokkan fisik barang, dan mendokumentasikan foto serah terima di sistem.", styles['TableCell'])],
    ]
    notif_guide_table = Table(notif_guide_data, colWidths=[120, 385])
    notif_guide_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), C_NAVY_MID),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('TOPPADDING', (0,0), (-1,-1), 3),
        ('BOTTOMPADDING', (0,0), (-1,-1), 3),
        ('LEFTPADDING', (0,0), (-1,-1), 5),
        ('RIGHTPADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(notif_guide_table)

    story.append(Spacer(1, 4))
    story.append(make_callout(
        "Kecepatan Respon Distribusi Barang",
        "Notifikasi real-time memastikan tidak ada permohonan dinas yang tertahan lama di gudang. "
        "PIC diharapkan merespons notifikasi dan mendistribusikan barang fisik dalam waktu maksimal 1 hari kerja setelah tiket disetujui.",
        kind="success",
        styles=styles,
        width=505
    ))

    story.append(PageBreak())

    # --------------------------------------------------------------------------
    # HALAMAN 13: BAB XI - SOP DISTRIBUSI FISIK BMN & FAQ PIC
    # --------------------------------------------------------------------------
    story.extend(make_header_bar("BAB XI: SOP DISTRIBUSI FISIK BMN & TANYA JAWAB (FAQ)", styles))
    
    story.append(Paragraph("11.1 Standar Operasional Prosedur (SOP) Penyerahan Fisik Barang", styles['SubChapterHeading']))
    story.append(Paragraph(
        "1. <b>Verifikasi Tiket :</b> Pastikan tiket pengajuan telah berstatus <i>'Disetujui Kasubag TU'</i> sebelum barang dikeluarkan dari gudang.<br/>"
        "2. <b>Pemeriksaan Fisik :</b> Cek keutuhan kemasan, tanggal kedaluwarsa (tinta/baterai), dan kelayakan pakai barang ATK.<br/>"
        "3. <b>Penyerahan di Ruangan :</b> PIC menyerahkan barang langsung kepada staf pemohon atau perwakilan subdirektorat.<br/>"
        "4. <b>Dokumentasi Wajib :</b> Ambil foto penyerahan fisik barang secara jelas yang memperlihatkan barang dan penerima.<br/>"
        "5. <b>Penerbitan Tanda Terima :</b> Tekan tombol konfirmasi pada sistem agar berkas beralih status menjadi <i>'Selesai Diserahkan'</i>.",
        styles['BodyJustify']
    ))

    story.append(Spacer(1, 3))
    story.append(Paragraph("11.2 Tanya Jawab Operasional PIC Gudang (FAQ)", styles['SubChapterHeading']))

    faq_list = [
        (
            "T: Bagaimana jika pemohon menolak menerima barang karena ada cacat/rusak fisik?",
            "J: Jangan selesaikan proses serah terima di sistem. Bawa kembali barang rusak ke gudang, ganti dengan stok fisik yang baik, "
            "dan lakukan penyerahan ulang. Catat barang rusak dalam pembukuan kondisi gudang."
        ),
        (
            "T: Mengapa akun PIC tidak bisa mendaftar sendiri di halaman registrasi umum?",
            "J: Sesuai tata kelola keamanan BMN Kementerian PKP RI, akun PIC Pengelola Gudang hanya dapat diterbitkan secara resmi "
            "oleh Kepala Subbagian Tata Usaha untuk memastikan integritas wewenang fisik gudang."
        ),
        (
            "T: Bagaimana jika terjadi selisih antara stok sistem dengan fisik barang riil di rak gudang?",
            "J: Lakukan pemeriksaan pada buku Transaksi Barang Masuk dan Barang Keluar untuk menelusuri faktur atau tiket yang belum tercatat. "
            "Koordinasikan dengan Kasubag TU untuk penyesuaian catatan stock opname."
        ),
        (
            "T: Apakah petugas PIC boleh mencetak laporan rekapitulasi untuk keperluan audit sewaktu-waktu?",
            "J: Sangat diperbolehkan. Gunakan menu 'Laporan & Arsip' atau 'Rekapitulasi Arsip' untuk mencetak Berita Acara resmi, "
            "ekspor Excel, maupun dokumen PDF berkop Kementerian PKP RI kapan pun dibutuhkan."
        ),
    ]

    for q, a in faq_list:
        story.append(Paragraph(f"<b>{q}</b>", styles['SubSubHeading']))
        story.append(Paragraph(a, styles['BodyJustify']))
        story.append(Spacer(1, 1))

    story.append(Spacer(1, 3))
    story.append(Paragraph("11.3 Saluran Bantuan Teknis & Koordinasi Kasubag TU", styles['SubChapterHeading']))

    help_data = [
        [Paragraph("<b>Unit Pembina</b>", styles['TableCellBold']), Paragraph(": Subbagian Tata Usaha, Direktorat Jenderal Perumahan, Kementerian PKP RI", styles['TableCell'])],
        [Paragraph("<b>Lokasi Gudang</b>", styles['TableCellBold']), Paragraph(": Ruang Tata Usaha & Gudang Logistik ATK, Gedung Kementerian PKP RI, Jakarta", styles['TableCell'])],
        [Paragraph("<b>Kontak Koordinasi</b>", styles['TableCellBold']), Paragraph(": Kepala Subbagian Tata Usaha (Penyetuju & Pengelola Akun PIC)", styles['TableCell'])],
        [Paragraph("<b>Jam Operasional</b>", styles['TableCellBold']), Paragraph(": Senin &ndash; Jumat, Pukul 08.30 &ndash; 16.00 WIB (Hari Kerja Kedinasan)", styles['TableCell'])],
    ]
    help_table = Table(help_data, colWidths=[120, 385])
    help_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), C_BG_LIGHT),
        ('BOX', (0,0), (-1,-1), 1, C_BORDER_GRAY),
        ('INNERGRID', (0,0), (-1,-1), 0.5, C_BORDER_GRAY),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
        ('TOPPADDING', (0,0), (-1,-1), 2.5),
        ('BOTTOMPADDING', (0,0), (-1,-1), 2.5),
        ('LEFTPADDING', (0,0), (-1,-1), 6),
        ('RIGHTPADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(help_table)

    story.append(Spacer(1, 5))
    story.append(make_callout(
        "Integritas Pengelolaan Barang Milik Negara (BMN)",
        "Ketelitian dan kedisiplinan petugas PIC dalam mencatat mutasi fisik dan menyerahkan barang menjadi fondasi utama "
        "terciptanya tata kelola logistik persediaan yang bersih, transparan, dan akuntabel di Kementerian PKP RI.",
        kind="info",
        styles=styles,
        width=505
    ))

    return story


# ==============================================================================
# 5. FUNGSI EKSEKUSI PEMBUATAN PDF
# ==============================================================================
def generate_pdf(output_filename="Manual_Book_Ateka_PIC.pdf"):
    print(f"[*] Menyiapkan pembuatan PDF: {output_filename}")
    
    base_dir = os.path.dirname(os.path.abspath(__file__))
    logo_path = os.path.join(base_dir, "public", "hope-ui", "assets", "images", "auth", "kemenpkp-logo.png")
    img_dir = os.path.join(base_dir, "assets", "manual_book_pic_images")
    pdf_path = os.path.join(base_dir, output_filename)

    # Konfigurasi A4: 595.27 x 842 pt. Margin 45 pt kiri/kanan -> printable width 505.27 pt
    doc = SimpleDocTemplate(
        pdf_path,
        pagesize=A4,
        leftMargin=45,
        rightMargin=45,
        topMargin=46,
        bottomMargin=46,
        title="Buku Panduan Pengguna ATEKA - Role PIC Distribusi & Gudang",
        author="Subbagian Tata Usaha Kementerian PKP RI",
        subject="User Manual Book Role PIC Distribusi & Pengelola Gudang",
        creator="Sistem Inventori ATEKA Kementerian PKP RI"
    )

    styles = create_custom_styles()
    story = build_manual_book_story(styles, logo_path, img_dir)

    print(f"[*] Membangun dokumen dengan {len(story)} elemen Platypus...")
    doc.build(story, canvasmaker=AtekaNumberedCanvas)
    
    file_size_kb = os.path.getsize(pdf_path) / 1024
    print(f"[SUCCESS] PDF PIC berhasil diterbitkan: {pdf_path} ({file_size_kb:.2f} KB)")
    return pdf_path


if __name__ == "__main__":
    out_file = sys.argv[1] if len(sys.argv) > 1 else "Manual_Book_Ateka_PIC.pdf"
    generate_pdf(out_file)
