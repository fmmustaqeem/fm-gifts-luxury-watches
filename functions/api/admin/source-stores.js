import { json, verifySession } from "../../api/auth.js";
import { FM_STORE_FEED, FM_MARGIN_RATE } from "./_feed.js";

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
  return json({ ok: true, feed: FM_STORE_FEED, marginRate: FM_MARGIN_RATE });
}
