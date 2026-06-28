import requests

import os

from dotenv import load_dotenv

from core.exceptions import AppException



load_dotenv()



OPENROUTER_KEY = os.getenv("OPENROUTER_KEY")




def call_openai(prompt):



    if not OPENROUTER_KEY:

        raise AppException("OPENROUTER_KEY missing", 500)



    url = "https://openrouter.ai/api/v1/chat/completions"



    headers = {

        "Authorization": f"Bearer {OPENROUTER_KEY}",

        "Content-Type": "application/json"

    }



    response = requests.post(url, json={

        "model": "openai/gpt-4o-mini",

        "messages": [

            {"role": "user", "content": prompt}

        ]

    }, headers=headers, timeout=60)



    if response.status_code != 200:

        raise AppException("OpenAI API failed", 500)



    data = response.json()



    return data["choices"][0]["message"]["content"]