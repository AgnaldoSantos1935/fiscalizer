from fastapi import FastAPI
from pydantic import BaseModel
import base64
import tempfile
import fitz
import pytesseract
from openai import OpenAI
import json
import re

app = FastAPI()
client = OpenAI()

class Documento(BaseModel):
    file_base64: str
    filename: str
    demanda_id: int

@app.post("/processar")
def processar_documento(data: Documento):

    # salva arquivo temporário
    binario = base64.b64decode(data.file_base64)
    tmp = tempfile.NamedTemporaryFile(delete=False)
    tmp.write(binario)
    tmp.close()

    # EXTRAÇÃO AUTOMÁTICA DE TEXTO
    texto_final = extrair_texto(tmp.name)

    # CHAMA A IA
    resposta = client.chat.completions.create(
        model="gpt-4.1",
        messages=[
            {"role": "system", "content": "Você é um especialista em engenharia de requisitos, PF e auditoria de software."},
            {"role": "user", "content": f"Analise o seguinte documento técnico e gere o JSON oficial:\n\n{texto_final}"}
        ]
    )

    conteudo = resposta.choices[0].message["content"]

    # IA retorna JSON dentro do texto
    json_str = extrair_json(conteudo)
    return json.loads(json_str)


def extrair_texto(caminho):
    try:
        texto = ""
        with fitz.open(caminho) as pdf:
            for pagina in pdf:
                texto += pagina.get_text()

        # fallback OCR
        if len(texto.strip()) < 50:
            texto = pytesseract.image_to_string(caminho)

        return texto
    except:
        return ""


def extrair_json(texto):
    """
    Extrai o primeiro bloco JSON válido da resposta da IA.
    """
    match = re.search(r"\{[\s\S]+\}", texto)
    return match.group(0) if match else "{}"
