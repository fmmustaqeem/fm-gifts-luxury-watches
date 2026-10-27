import { verifyPassword, normalizeEmail, parseJson, json, createSession } from "./auth.js";

export async function onRequestPost({ request, env }) {
  const input = await parseJson(request);
  const email = normalizeEmail(input.email);
  const password = String(input.password || "");

  if (!email || !password) {
    return json({ ok: false, error: "Email and password are required" }, 400);
  }

  if (!env?.DB || !env?.SESSION_SECRET) {
    return json({ ok: false, error: "Authentication service is not configured" }, 503);
  }

  const user = await env.DB
    .prepare("SELECT id,name,email,password_hash,role,tier,status FROM users WHERE email=? LIMIT 1")
    .bind(email)
    .first();

  if (
    !user ||
    user.status !== "active" ||
    !(await verifyPassword(password, user.password_hash))
  ) {
    return json({ ok: false, error: "Invalid credentials" }, 401);
  }

  const token = await createSession(user, env.SESSION_SECRET);
  return new Response(
    JSON.stringify({
      ok: true,
      user: {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        tier: user.tier,
      },
    }),
    {
      status: 200,
      headers: {
        "content-type": "application/json",
        "set-cookie": `fm_session=${token}; Max-Age=28800; Path=/; HttpOnly; Secure; SameSite=Lax`,
      },
    },
  );
}
