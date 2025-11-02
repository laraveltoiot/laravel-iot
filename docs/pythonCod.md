## cod 1
```python
from machine import Pin
import time
import network
import urequests
import ujson
import random

# ====== CONFIGURE THESE ======
SSID = "supertest1"
PASSWORD = "11111111"
API_URL = "pagina ta"
# =============================

LED = Pin("LED", Pin.OUT)


def led_blink(times=3, on_ms=100, off_ms=100):
    for _ in range(times):
        LED.on()
        time.sleep_ms(on_ms)
        LED.off()
        time.sleep_ms(off_ms)


def wifi_connect(ssid, password, timeout_ms=15000):
    wlan = network.WLAN(network.STA_IF)
    if not wlan.active():
        wlan.active(True)
    if not wlan.isconnected():
        print("Connecting to WiFi:", ssid)
        wlan.connect(ssid, password)
        start = time.ticks_ms()
        # Blink while trying to connect
        state = 0
        while (not wlan.isconnected()) and (time.ticks_diff(time.ticks_ms(), start) < timeout_ms):
            state ^= 1
            LED.value(state)
            time.sleep_ms(200)
        LED.off()
    if wlan.isconnected():
        print("Connected! IP:", wlan.ifconfig()[0])
        led_blink(2, 200, 120)
        return wlan
    else:
        print("WiFi connection failed.")
        return None


def post_random_value():
    value = random.randint(0, 1_000_000)
    try:
        payload = {"value": value}
        resp = urequests.post(
            API_URL,
            data=ujson.dumps(payload),
            headers={"Content-Type": "application/json"},
        )
        # urequests may use .status_code or .status depending on version
        status = getattr(resp, "status_code", getattr(resp, "status", None))
        # Try to read response body before closing
        try:
            resp_text = resp.text
        except Exception:
            try:
                resp_text = resp.content
            except Exception:
                resp_text = None
        print("POST:", payload, "->", status, "; response:", resp_text)
        resp.close()
        # Short blink to indicate a successful send
        LED.on()
        time.sleep_ms(50)
        LED.off()
    except Exception as e:
        print("Error posting:", e)


def main():
    wlan = None
    # Connect (and keep retrying) to WiFi
    while wlan is None or not wlan.isconnected():
        wlan = wifi_connect(SSID, PASSWORD)
        if wlan is None:
            print("Retry WiFi in 3 seconds...")
            time.sleep(3)

    # Send a random number every 3 seconds
    while True:
        if not wlan.isconnected():
            print("WiFi dropped; reconnecting...")
            wlan = wifi_connect(SSID, PASSWORD)
            continue
        post_random_value()
        time.sleep(3)


if __name__ == "__main__":
    main()

```

## cod2 
```python
from machine import Pin
import time
import network
import urequests
import ujson

# ====== CONFIGURE THESE ======
SSID = "supertest1"
PASSWORD = "11111111"
TASKS_URL = "dasdsadsa"
STATUS_URL = "dsadsadsa"
# =============================

LED = Pin("LED", Pin.OUT)


def led_blink(times=3, on_ms=100, off_ms=100):
    for _ in range(times):
        LED.on()
        time.sleep_ms(on_ms)
        LED.off()
        time.sleep_ms(off_ms)


def wifi_connect(ssid, password, timeout_ms=15000):
    wlan = network.WLAN(network.STA_IF)
    if not wlan.active():
        wlan.active(True)
    if not wlan.isconnected():
        print("Connecting to WiFi:", ssid)
        wlan.connect(ssid, password)
        start = time.ticks_ms()
        # Blink while trying to connect
        state = 0
        while (not wlan.isconnected()) and (time.ticks_diff(time.ticks_ms(), start) < timeout_ms):
            state ^= 1
            LED.value(state)
            time.sleep_ms(200)
        LED.off()
    if wlan.isconnected():
        print("Connected! IP:", wlan.ifconfig()[0])
        led_blink(2, 200, 120)
        return wlan
    else:
        print("WiFi connection failed.")
        return None


def fetch_tasks():
    try:
        resp = urequests.get(TASKS_URL)
        status = getattr(resp, "status_code", getattr(resp, "status", None))
        # Read and parse body safely
        try:
            data = resp.json()
        except Exception:
            try:
                text = getattr(resp, "text", None)
                if text is None:
                    text = getattr(resp, "content", b"")
                    if isinstance(text, bytes):
                        text = text.decode()
                data = ujson.loads(text)
            except Exception:
                data = None
        resp.close()
        if status != 200 or not isinstance(data, dict):
            print("GET tasks failed:", status, data)
            return []
        tasks = data.get("tasks") or []
        print("Tasks fetched:", len(tasks))
        return tasks
    except Exception as e:
        print("Error fetching tasks:", e)
        return []


def update_task_status(task_id, status):
    try:
        payload = {"id": int(task_id), "status": status}
        resp = urequests.post(
            STATUS_URL,
            data=ujson.dumps(payload),
            headers={"Content-Type": "application/json"},
        )
        code = getattr(resp, "status_code", getattr(resp, "status", None))
        try:
            body = getattr(resp, "text", None)
            if body is None:
                body = getattr(resp, "content", None)
        except Exception:
            body = None
        resp.close()
        print("Status POST:", payload, "->", code, "; resp:", body)
        return code and 200 <= int(code) < 300
    except Exception as e:
        print("Error updating status:", e)
        return False


def handle_task(task):
    try:
        cmd = task.get("command")
        payload = task.get("payload") or {}
        if cmd == "set_led":
            pin = payload.get("pin")
            state = (payload.get("state") or "").lower()
            if pin in ("onboard", "led", "LED"):
                if state == "on":
                    LED.on()
                elif state == "off":
                    LED.off()
                else:
                    raise ValueError("Unsupported LED state: %s" % state)
                return True
            else:
                raise ValueError("Unsupported pin: %s" % pin)
        else:
            raise ValueError("Unsupported command: %s" % cmd)
    except Exception as e:
        print("Task handling error:", e)
        return False


def main():
    wlan = None
    # Connect (and keep retrying) to WiFi
    while wlan is None or not wlan.isconnected():
        wlan = wifi_connect(SSID, PASSWORD)
        if wlan is None:
            print("Retry WiFi in 3 seconds...")
            time.sleep(3)

    # Poll for tasks every 5 seconds
    while True:
        if not wlan.isconnected():
            print("WiFi dropped; reconnecting...")
            wlan = wifi_connect(SSID, PASSWORD)
            continue
        tasks = fetch_tasks()
        for task in tasks:
            try:
                status = (task.get("status") or "").lower()
                if status != "pending":
                    continue
                task_id = task.get("id")
                print("Processing task", task_id, "command:", task.get("command"))
                ok = handle_task(task)
                new_status = "done" if ok else "failed"
                update_task_status(task_id, new_status)
            except Exception as e:
                print("Task loop error:", e)
        time.sleep(5)


if __name__ == "__main__":
    main()

``
