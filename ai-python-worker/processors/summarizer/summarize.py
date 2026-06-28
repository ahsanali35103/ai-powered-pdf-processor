from processors.summarizer.api_clients import call_openai
from core.exceptions import AppException


def summarize_text(text):

    if not text:
        raise AppException("No text provided", 400)

    prompt = f"""
Summarize the following text.

Rules:
- Keep it concise
- Preserve important meaning
- No explanation
- Return only summarized text

Text:
{text}
"""

    return call_openai(prompt)