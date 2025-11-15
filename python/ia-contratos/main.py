from fastapi import FastAPI
from pydantic import BaseModel
import base64
import tempfile
from services.extract_text import extract_text
from services.contract_parser import parse_contract_to_json

app = FastAPI()

class ContratoInput(BaseModel):
    file_base64: str
    filename: str

@app.post("/processar-contrato")
def processar_contrato(data: ContratoInput):
    binario = base64.b64decode(data.file_base64)
    tmp = tempfile.NamedTemporaryFile(delete=False)
    tmp.write(binario)
    tmp.close()

    texto = extract_text(tmp.name, data.filename)
    json_contrato = parse_contract_to_json(texto)

    return json_contrato
