from openai import OpenAI
import re
import json
import os

client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))

JSON_CONTRATO_PROMPT = """
Você é um especialista em contratos administrativos, Lei 8.666/93 e Lei 14.133/21.

Dado o texto integral de um CONTRATO ADMINISTRATIVO, você deve produzir EXCLUSIVAMENTE
um JSON com o seguinte formato (NÃO explique nada, NÃO coloque comentários):

{
  "contrato": {
    "numero": "",
    "processo_origem": "",
    "modalidade": "",
    "objeto": "",
    "objeto_resumido": "",
    "valor_global": 0,
    "valor_mensal": 0,
    "quantidade_meses": 0,
    "data_assinatura": "",
    "data_inicio_vigencia": "",
    "data_fim_vigencia": "",
    "empresa": {
      "razao_social": "",
      "cnpj": "",
      "endereco": "",
      "representante": "",
      "telefone": "",
      "email": ""
    },
    "gestores": {
      "fiscal_tecnico": "",
      "fiscal_administrativo": "",
      "gestor": ""
    },
    "objeto_detalhado": "",
    "obrigações_contratada": [],
    "obrigações_contratante": [],
    "itens_fornecimento": [],
    "anexos": [],
    "clausulas": {
      "reajuste": "",
      "garantia": "",
      "forma_pagamento": "",
      "metas": "",
      "indicadores": "",
      "confidencialidade": "",
      "rescisao": "",
      "sanções": ""
    },
    "riscos_detectados": [],
    "inconsistencias": []
  }
}
"""

def extract_json_from_text(text: str) -> str:
    m = re.search(r"\{[\s\S]+\}", text)
    return m.group(0) if m else "{}"

def parse_contract_to_json(texto_contrato: str) -> dict:
    completion = client.chat.completions.create(
        model="gpt-4.1",
        messages=[
            {"role": "system", "content": JSON_CONTRATO_PROMPT},
            {"role": "user", "content": texto_contrato[:60000]}  # corta se for gigante
        ]
    )
    content = completion.choices[0].message.content
    json_str = extract_json_from_text(content)
    try:
        return json.loads(json_str)
    except Exception:
        return {"contrato": {}, "erro_parse": True}
