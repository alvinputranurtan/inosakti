# MQTT Architecture Guide (Basic IoT ESP32)

## Topology
- ESP32 device publishes telemetry to broker.
- Dashboard/web backend subscribes telemetry.
- Dashboard publishes command topic for actuator control.

## Topic Naming Convention
- `inosakti/esp32/{device_id}/telemetry`
- `inosakti/esp32/{device_id}/status`
- `inosakti/esp32/{device_id}/command`

## Payload Example
```json
{
  "device_id": "esp32-lab-01",
  "temperature_c": 28.4,
  "humidity_pct": 68.2,
  "uptime_s": 4321,
  "ts": "2026-02-22T11:25:00+07:00"
}
```

## Reliability Notes
- Use reconnect loop with exponential backoff.
- Keep payload compact to reduce bandwidth.
- Start with QoS 0 for telemetry and QoS 1 for commands.
- Validate sensor range before publish.

