package dev.skyblock.util;

public class Format {
    public static String formatMoney(double amount) {
        if (amount >= 1_000_000_000) return String.format("%.1fB", amount / 1_000_000_000);
        if (amount >= 1_000_000)     return String.format("%.1fM", amount / 1_000_000);
        if (amount >= 1_000)         return String.format("%.1fK", amount / 1_000);
        return String.format("%.1f", amount);
    }
}
