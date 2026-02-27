import Swal from "sweetalert2";

const baseConfig = {
    confirmButtonColor: "#4f46e5",
};

export const showError = (text, title = "Oops...") => {
    return Swal.fire({
        ...baseConfig,
        icon: "error",
        title,
        text,
    });
};

export const showSuccess = (text, title = "Berhasil") => {
    return Swal.fire({
        ...baseConfig,
        icon: "success",
        title,
        text,
    });
};

export const confirmAction = ({
    title = "Yakin?",
    text = "Aksi ini tidak dapat dibatalkan.",
    confirmButtonText = "Ya, lanjut",
    cancelButtonText = "Batal",
    icon = "warning",
} = {}) => {
    return Swal.fire({
        ...baseConfig,
        icon,
        title,
        text,
        showCancelButton: true,
        confirmButtonText,
        cancelButtonText,
        reverseButtons: true,
    });
};
