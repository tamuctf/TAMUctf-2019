	.section	__TEXT,__text,regular,pure_instructions
	.build_version macos, 10, 14
	.globl	_concat                 ## -- Begin function concat
	.p2align	4, 0x90
_concat:                                ## @concat
	.cfi_startproc
## %bb.0:
	pushq	%rbp
	.cfi_def_cfa_offset 16
	.cfi_offset %rbp, -16
	movq	%rsp, %rbp
	.cfi_def_cfa_register %rbp
	subq	$48, %rsp
	movq	%rdi, -8(%rbp)
	movq	%rsi, -16(%rbp)
	movq	-8(%rbp), %rdi
	callq	_strlen
	movq	-16(%rbp), %rdi
	movq	%rax, -32(%rbp)         ## 8-byte Spill
	callq	_strlen
	movq	-32(%rbp), %rsi         ## 8-byte Reload
	addq	%rax, %rsi
	addq	$1, %rsi
	movq	%rsi, %rdi
	callq	_malloc
	movq	$-1, %rdx
	movq	%rax, -24(%rbp)
	movq	-24(%rbp), %rdi
	movq	-8(%rbp), %rsi
	callq	___strcpy_chk
	movq	$-1, %rdx
	movq	-24(%rbp), %rdi
	movq	-16(%rbp), %rsi
	movq	%rax, -40(%rbp)         ## 8-byte Spill
	callq	___strcpy_chk
	movq	-24(%rbp), %rdx
	movq	%rax, -48(%rbp)         ## 8-byte Spill
	movq	%rdx, %rax
	addq	$48, %rsp
	popq	%rbp
	retq
	.cfi_endproc
                                        ## -- End function
	.globl	_main                   ## -- Begin function main
	.p2align	4, 0x90
_main:                                  ## @main
	.cfi_startproc
## %bb.0:
	pushq	%rbp
	.cfi_def_cfa_offset 16
	.cfi_offset %rbp, -16
	movq	%rsp, %rbp
	.cfi_def_cfa_register %rbp
	subq	$80, %rsp
	leaq	L_.str(%rip), %rdi
	movl	$3, %eax
	movl	$14, %ecx
	xorl	%esi, %esi
	movl	$8, %edx
                                        ## kill: def %rdx killed %edx
	leaq	-16(%rbp), %r8
	movq	___stack_chk_guard@GOTPCREL(%rip), %r9
	movq	(%r9), %r9
	movq	%r9, -8(%rbp)
	movl	$0, -20(%rbp)
	movq	%rdi, -56(%rbp)         ## 8-byte Spill
	movq	%r8, %rdi
	movl	%ecx, -60(%rbp)         ## 4-byte Spill
	movl	%eax, -64(%rbp)         ## 4-byte Spill
	callq	_memset
	movb	$65, -16(%rbp)
	movb	$53, -15(%rbp)
	movb	$53, -14(%rbp)
	movb	$51, -13(%rbp)
	movb	$77, -12(%rbp)
	movb	$98, -11(%rbp)
	movb	$49, -10(%rbp)
	movb	$89, -9(%rbp)
	movl	$0, -28(%rbp)
	movl	$1, -32(%rbp)
	movl	$2, -36(%rbp)
	movl	-36(%rbp), %eax
	imull	-36(%rbp), %eax
	imull	-36(%rbp), %eax
	movl	-28(%rbp), %ecx
	addl	-32(%rbp), %ecx
	addl	-32(%rbp), %ecx
	addl	-32(%rbp), %ecx
	imull	%ecx, %eax
	cltd
	movl	-60(%rbp), %ecx         ## 4-byte Reload
	idivl	%ecx
	movl	%eax, -40(%rbp)
	movl	-36(%rbp), %eax
	imull	-36(%rbp), %eax
	imull	-36(%rbp), %eax
	movl	-28(%rbp), %esi
	addl	-32(%rbp), %esi
	addl	-32(%rbp), %esi
	imull	%esi, %eax
	cltd
	movl	-64(%rbp), %esi         ## 4-byte Reload
	idivl	%esi
	movl	%eax, -44(%rbp)
	movl	-40(%rbp), %esi
	movq	-56(%rbp), %rdi         ## 8-byte Reload
	movb	$0, %al
	callq	_printf
	leaq	L_.str.1(%rip), %rdi
	movl	-44(%rbp), %esi
	movl	%eax, -68(%rbp)         ## 4-byte Spill
	movb	$0, %al
	callq	_printf
	leaq	L_.str.2(%rip), %rdi
	leaq	-16(%rbp), %rsi
	movl	%eax, -72(%rbp)         ## 4-byte Spill
	movb	$0, %al
	callq	_printf
	movq	___stack_chk_guard@GOTPCREL(%rip), %rsi
	movq	(%rsi), %rsi
	movq	-8(%rbp), %rdi
	cmpq	%rdi, %rsi
	movl	%eax, -76(%rbp)         ## 4-byte Spill
	jne	LBB1_2
## %bb.1:
	xorl	%eax, %eax
	addq	$80, %rsp
	popq	%rbp
	retq
LBB1_2:
	callq	___stack_chk_fail
	ud2
	.cfi_endproc
                                        ## -- End function
	.section	__TEXT,__cstring,cstring_literals
L_.str:                                 ## @.str
	.asciz	"The answer: %d\n"

L_.str.1:                               ## @.str.1
	.asciz	"Maybe it's this:%d\n"

L_.str.2:                               ## @.str.2
	.asciz	"gigem{%s}\n"


.subsections_via_symbols
