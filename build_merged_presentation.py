import os
import pptx
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE

prs = pptx.Presentation()
prs.slide_width = Inches(13.333)
prs.slide_height = Inches(7.5)
blank_layout = prs.slide_layouts[6]

TEMPLATE_BG = 'slide_template_clean.png'

# Color Palette
COLOR_GOLD = RGBColor(212, 195, 139)       # #D4C38B template golden header text
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
COLOR_ACCENT_RED = RGBColor(220, 38, 38)   # #DC2626
COLOR_LIGHT_BLUE = RGBColor(239, 246, 255) # #EFF6FF
COLOR_LIGHT_TEAL = RGBColor(240, 253, 250) # #F0FDFA
COLOR_LIGHT_AMBER = RGBColor(254, 243, 199)# #FEF3C7

def add_base_slide(header_title, slide_number):
    slide = prs.slides.add_slide(blank_layout)
    slide.shapes.add_picture(TEMPLATE_BG, 0, 0, Inches(13.333), Inches(7.5))
    
    # Header title text
    tb = slide.shapes.add_textbox(Inches(0.55), Inches(0.28), Inches(8.5), Inches(0.55))
    tf = tb.text_frame
    tf.word_wrap = True
    tf.margin_left = tf.margin_top = tf.margin_right = tf.margin_bottom = 0
    p = tf.paragraphs[0]
    p.text = header_title.upper()
    p.font.name = 'Arial'
    p.font.size = Pt(19)
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

def add_bullet_point(tf, bold_prefix, text_content, font_size=11, space_after=4):
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

print("Helper functions ready.")
