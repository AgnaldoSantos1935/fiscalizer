import fitz  # PyMuPDF
import docx
import os
import pytesseract
from PIL import Image
import tempfile

def extract_text_from_pdf(path: str) -> str:
    text = ""
    with fitz.open(path) as doc:
        for page in doc:
            text += page.get_text()
    if len(text.strip()) < 50:
        # fallback OCR (para PDF imagem)
        text = ocr_pdf_to_text(path)
    return text

def ocr_pdf_to_text(path: str) -> str:
    # versão simplificada: converte páginas em imagens e roda ocr
    import fitz
    text = ""
    with fitz.open(path) as doc:
        for page_index in range(len(doc)):
            page = doc[page_index]
            pix = page.get_pixmap()
            with tempfile.NamedTemporaryFile(suffix=".png", delete=False) as img_tmp:
                img_tmp.write(pix.tobytes("png"))
                img_tmp.flush()
                text += pytesseract.image_to_string(Image.open(img_tmp.name))
                os.unlink(img_tmp.name)
    return text

def extract_text_from_docx(path: str) -> str:
    d = docx.Document(path)
    return "\n".join(p.text for p in d.paragraphs)

def extract_text(path: str, filename: str) -> str:
    filename_lower = filename.lower()
    if filename_lower.endswith(".pdf"):
        return extract_text_from_pdf(path)
    elif filename_lower.endswith(".docx"):
        return extract_text_from_docx(path)
    else:
        # fallback ruim mas funciona: tenta como PDF
        return extract_text_from_pdf(path)
