from .api_client import call_openai

from core.exceptions import AppException




def translate_text(text, target_language):



    if not text:

        raise AppException("No text provided", 400)



    prompt = f"""

Translate the following text into {target_language}.



Rules:

- Only translated text

- No explanation



Text:

{text}

"""



    return call_openai(prompt)
