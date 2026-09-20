package com.rasamandeh.rasa_mandeh

import android.content.ContentValues
import android.content.Intent
import android.net.Uri
import android.os.Build
import android.os.Environment
import android.provider.MediaStore
import androidx.core.content.FileProvider
import io.flutter.embedding.android.FlutterActivity
import io.flutter.embedding.engine.FlutterEngine
import io.flutter.plugin.common.MethodChannel
import java.io.File
import java.io.FileOutputStream

class MainActivity : FlutterActivity() {
    private val CHANNEL = "com.rasamandeh.rasa_mandeh/receipt"

    override fun configureFlutterEngine(flutterEngine: FlutterEngine) {
        super.configureFlutterEngine(flutterEngine)

        MethodChannel(flutterEngine.dartExecutor.binaryMessenger, CHANNEL).setMethodCallHandler { call, result ->
            when (call.method) {
                "saveReceiptPdf" -> {
                    try {
                        val fileName = call.argument<String>("fileName") ?: "struk_rasa_mandeh.pdf"
                        val bytes = call.argument<ByteArray>("bytes") ?: byteArrayOf()
                        val filePath = savePdf(fileName, bytes)
                        result.success(filePath)
                    } catch (e: Exception) {
                        result.error("SAVE_FAILED", e.localizedMessage, null)
                    }
                }
                "openReceiptPdf" -> {
                    try {
                        val fileName = call.argument<String>("fileName") ?: "struk_rasa_mandeh.pdf"
                        val bytes = call.argument<ByteArray>("bytes")
                        val file = if (bytes != null && bytes.isNotEmpty()) {
                            val tempFile = File(cacheDir, fileName)
                            tempFile.writeBytes(bytes)
                            tempFile
                        } else {
                            File(cacheDir, fileName)
                        }
                        openPdfFile(file)
                        result.success(true)
                    } catch (e: Exception) {
                        result.error("OPEN_FAILED", e.localizedMessage, null)
                    }
                }
                "shareReceiptPdf" -> {
                    try {
                        val fileName = call.argument<String>("fileName") ?: "struk_rasa_mandeh.pdf"
                        val bytes = call.argument<ByteArray>("bytes")
                        val file = if (bytes != null && bytes.isNotEmpty()) {
                            val tempFile = File(cacheDir, fileName)
                            tempFile.writeBytes(bytes)
                            tempFile
                        } else {
                            File(cacheDir, fileName)
                        }
                        sharePdfFile(file)
                        result.success(true)
                    } catch (e: Exception) {
                        result.error("SHARE_FAILED", e.localizedMessage, null)
                    }
                }
                else -> result.notImplemented()
            }
        }
    }

    private fun savePdf(fileName: String, bytes: ByteArray): String {
        // Simpan selalu ke cache internal untuk kemudahan akses intent
        val cacheFile = File(cacheDir, fileName)
        cacheFile.writeBytes(bytes)

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            val values = ContentValues().apply {
                put(MediaStore.MediaColumns.DISPLAY_NAME, fileName)
                put(MediaStore.MediaColumns.MIME_TYPE, "application/pdf")
                put(MediaStore.MediaColumns.RELATIVE_PATH, Environment.DIRECTORY_DOWNLOADS)
            }
            val uri = contentResolver.insert(MediaStore.Downloads.EXTERNAL_CONTENT_URI, values)
            if (uri != null) {
                contentResolver.openOutputStream(uri)?.use { os ->
                    os.write(bytes)
                    os.flush()
                }
                return "Download/$fileName"
            }
        } else {
            val downloadDir = Environment.getExternalStoragePublicDirectory(Environment.DIRECTORY_DOWNLOADS)
            if (!downloadDir.exists()) {
                downloadDir.mkdirs()
            }
            val destFile = File(downloadDir, fileName)
            FileOutputStream(destFile).use {
                it.write(bytes)
                it.flush()
            }
            return destFile.absolutePath
        }
        return cacheFile.absolutePath
    }

    private fun openPdfFile(file: File) {
        val uri: Uri = FileProvider.getUriForFile(
            this,
            "${applicationContext.packageName}.fileprovider",
            file
        )
        val intent = Intent(Intent.ACTION_VIEW).apply {
            setDataAndType(uri, "application/pdf")
            addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
            addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
        }
        startActivity(Intent.createChooser(intent, "Buka Struk Pembayaran"))
    }

    private fun sharePdfFile(file: File) {
        val uri: Uri = FileProvider.getUriForFile(
            this,
            "${applicationContext.packageName}.fileprovider",
            file
        )
        val intent = Intent(Intent.ACTION_SEND).apply {
            type = "application/pdf"
            putExtra(Intent.EXTRA_STREAM, uri)
            addFlags(Intent.FLAG_GRANT_READ_URI_PERMISSION)
        }
        startActivity(Intent.createChooser(intent, "Bagikan Struk Digital"))
    }
}

