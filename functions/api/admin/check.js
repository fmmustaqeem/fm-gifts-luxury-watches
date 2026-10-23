import { parseJson, json, verifySession } from "../../api/auth.js";

function readCookie(request, name) {
  const cookie = request.headers.get("Cookie") || "";
  const m = cookie.match(new RegExp("(?:^|;\\s*)" + name + "=([^;]+)"));
  return m ? decodeURIComponent(m[1]) : null;
}

export async function onRequestGet({ request, env }) {
  const secret = env?.SESSION_SECRET;
  if (!secret) return json({ ok: false, error: "Auth not configured" }, 503);
  const token = readCookie(request, "fm_session");
  const session = await verifySession(token, secret);
  if (!session) return json({ ok: false, error: "Not authenticated" }, 401);
  if (!["admin", "owner"].includes(session.role)) return json({ ok: false, error: "Forbidden" }, 403);
  return json({ ok: true, user: { id: session.sub, role: session.role, tier: session.tier } });
}

export async function onRequestPost(ctx) { return onRequestGet(ctx); }
